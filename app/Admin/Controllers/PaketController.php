<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Paket as PaketRepository;
use App\Models\Barang;
use App\Models\Paket;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;


class PaketController extends AdminController
{

    protected function grid()
    {
        Admin::css(asset('css/paket.css'));
        return Grid::make(new Paket(), function (Grid $grid) {

            $grid->model()->with(['detail.barang']);

            $grid->column('id')->sortable();
            $grid->column('nama_paket');
            $grid->column('deskripsi');
            $grid->column('status')
                ->display(function ($value) {

                    if ($value == 'aktif') {
                        return "<span class='status-aktif'>Aktif</span>";
                    }

                    return "<span class='status-nonaktif'>Nonaktif</span>";
                });
            $grid->column('detail', 'Jumlah Barang')
                ->display(function ($detail) {
                    return collect($detail)->sum('jumlah') . ' Barang';
                })
                ->expand(function () {

                    $rows = [];

                    foreach ($this->getRelation('detail') ?? [] as $item) {
                        $rows[] = [
                            'Barang' => optional($item->barang)->nama_barang,
                            'Jumlah' => $item->jumlah,
                        ];
                    }

                    return new \Dcat\Admin\Widgets\Table(
                        ['Barang', 'Jumlah'],
                        $rows
                    );
                });

            $grid->actions(function (Grid\Displayers\Actions $actions) {

                $actions->disableView();
                $actions->disableDelete();
            });
            $grid->batchActions(function (Grid\Tools\BatchActions $batch) {
                $batch->disableDelete();
            });
            $grid->disableRowSelector();
            $grid->quickSearch(function ($model, $keyword) {

                $model->where('nama_paket', 'like', "%{$keyword}%");
            });
        });
    }

    protected function form()
    {
        Admin::css(asset('css/paket.css'));

        return Form::make(
            PaketRepository::with(['detail']),
            function (Form $form) {
                $form->disableDeleteButton();
                $form->disableViewButton();
                $form->disableEditingCheck();
                $form->disableCreatingCheck();
                $form->disableViewCheck();

                $form->display('id', 'ID');

                $form->text('nama_paket', 'Nama Paket')
                    ->required();

                $form->select('status', 'Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->default('aktif')
                    ->required();

                $form->textarea('deskripsi', 'Deskripsi')
                    ->rows(4);

                $form->hasMany('detail', 'Detail Paket', function (Form\NestedForm $form) {

                    $form->select('barang_id', 'Nama Barang')
                        ->options(
                            Barang::where('status', 'aktif')
                                ->pluck('nama_barang', 'id')
                        )
                        ->required();

                    $form->number('jumlah', 'Jumlah')
                        ->min(1)
                        ->default(1)
                        ->rules('required|integer|min:1');
                })->useTable();
                $form->saving(function (Form $form) {

                    $detail = collect(request()->input('detail', []))
                        ->filter(function ($item) {

                            if (!empty($item['_remove_'])) {
                                return false;
                            }

                            return !empty($item['barang_id']);
                        })
                        ->values()
                        ->toArray();

                    if (count($detail) < 1) {
                        return $form->response()->error(
                            'Minimal tambahkan satu barang ke dalam paket.'
                        );
                    }

                    $barangDipilih = [];

                    foreach ($detail as $item) {

                        $barangId = $item['barang_id'];
                        $jumlah   = (int) ($item['jumlah'] ?? 0);

                        if ($jumlah < 1) {
                            return $form->response()->error(
                                'Jumlah barang minimal 1.'
                            );
                        }

                        if (in_array($barangId, $barangDipilih)) {
                            return $form->response()->error(
                                'Barang yang sama tidak boleh dipilih lebih dari satu kali.'
                            );
                        }

                        $barangDipilih[] = $barangId;

                        $barang = Barang::find($barangId);

                        if (!$barang) {
                            return $form->response()->error(
                                'Barang tidak ditemukan.'
                            );
                        }
                        if ($barang->status != 'aktif') {
                            return $form->response()->error(
                                "Barang {$barang->nama_barang} sudah nonaktif sehingga tidak dapat dimasukkan ke dalam paket."
                            );
                        }

                        if ($jumlah > $barang->jumlah_total) {
                            return $form->response()->error(
                                "Jumlah {$barang->nama_barang} melebihi stok fisik ({$barang->jumlah_total})."
                            );
                        }
                    }
                });
            }
        );
    }
}
