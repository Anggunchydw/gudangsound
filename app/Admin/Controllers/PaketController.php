<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Paket as PaketRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\Paket;
use App\Models\Barang;
use Dcat\Admin\Widgets\Table;

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
            // $grid->column('created_at');
            // $grid->column('updated_at')->sortable();

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
        return Form::make(
            PaketRepository::with(['detail']),
            function (Form $form) {
                $form->display('id');
                $form->text('nama_paket')->required();
                $form->textarea('deskripsi');

                // Detail paket dalam satu form
                $form->hasMany('detail', 'Isi Barang (Detail Paket)', function (Form\NestedForm $form) {
                    $form->select('barang_id', 'Barang')
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

                $form->display('created_at');
                $form->display('updated_at');
            }
        );
    }
}
