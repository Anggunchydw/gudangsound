<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Paket as PaketRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\Paket;
use App\Models\Barang;
use Dcat\Admin\Admin;

class PaketController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Paket(), function (Grid $grid) {
            $grid->model()->with(['detail.barang']);
            $grid->column('id')->sortable();
            $grid->column('nama_paket');
            $grid->column('deskripsi');

            $grid->column('detail', 'Jumlah Barang')
                ->display(function ($detail) {
                    $total = collect($detail)->sum('jumlah');
                    return $total . ' Barang';
                })
                ->expand(function () {
                    $rows = [];
                    $detailItems = $this->getRelation('detail') ?? [];
                    foreach ($detailItems as $item) {
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
            $grid->column('', 'Action')->display(function () {

                $id = $this->getKey();

                return "

        <a href='" . admin_url("paket/$id/edit") . "' title='Edit' style='margin-right:10px'>
            <i class='feather icon-edit'></i>
        </a>
    ";
            });
            $grid->disableActions();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
        });
    }


    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Paket(), function (Show $show) {
            // Eager load relasi detail + barang
            $show->model()->load('detail.barang');

            $show->field('id', 'ID');
            $show->field('nama_paket', 'Nama Paket');
            $show->field('deskripsi', 'Deskripsi');

            // Tampilkan tabel detail barang dalam paket
            $show->html(function () use ($show) {
                $rows = [];

                foreach ($show->model()->detail as $item) {
                    $rows[] = [
                        optional($item->barang)->nama_barang,
                        $item->jumlah,
                        optional($item->barang)->satuan,
                    ];
                }

                if (empty($rows)) {
                    return '<p class="text-muted">Belum ada barang dalam paket ini.</p>';
                }

                return new \Dcat\Admin\Widgets\Table(
                    ['Barang', 'Jumlah', 'Satuan'],
                    $rows
                );
            }, 'Daftar Barang');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */

    protected function form()
    {
        Admin::css(asset('css/paket.css'));

        return Form::make(
            PaketRepository::with(['detail']),
            function (Form $form) {

                $form->html('<div class="paket-form-wrapper">');

                $form->display('id', 'ID');

                $form->text('nama_paket', 'Nama Paket')
                    ->required();

                $form->textarea('deskripsi', 'Deskripsi')
                    ->rows(4);


                $form->hasMany('detail', 'Isi Barang (Detail Paket)', function (Form\NestedForm $form) {

                    $form->select('barang_id', 'Nama Barang')
                        ->options(
                            Barang::where('status', 'aktif')
                                ->pluck('nama_barang', 'id')
                        )
                        ->required();

                    $form->number('jumlah', 'Jumlah')
                        ->min(1)
                        ->default(1)
                        ->required();
                })->useTable();


                $form->saving(function (Form $form) {

                    $details = request()->input('detail');

                    foreach ($details as $detail) {

                        $barang = Barang::find($detail['barang_id']);

                        if ($barang && $detail['jumlah'] > $barang->jumlah_total) {

                            return $form->response()->error(
                                "Jumlah {$barang->nama_barang} melebihi stok tersedia ({$barang->jumlah_total})"
                            );
                        }
                    }
                });


                $form->html('</div>');
            }
        );
    }
}
