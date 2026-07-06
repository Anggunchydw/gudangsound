<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Barang;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use App\Services\InventoryService;
use Dcat\Admin\Http\Controllers\AdminController;

class BarangController extends AdminController

{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Barang(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('nama_barang');
            $grid->column('Kategori')->display(function ($value) {
                return "<span class='badge-kategori'>" . ucfirst($value) . "</span>";
            });
            $grid->column('satuan');
            $grid->column('jumlah_total');
            $grid->column('stok', 'Stok Hari Ini')
                ->display(function () {

                    return $this->getStokHariIniAttribute();
                });
            $grid->column('dipakai_hari_ini', 'Dipakai Hari Ini')
                ->display(function () {

                    return $this->getDipakaiHariIniAttribute();
                });
            $grid->column('status')->display(function ($value) {

                if ($value == 'aktif') {
                    return "<span class='status-aktif'>Aktif</span>";
                }

                return "<span class='status-nonaktif'>Nonaktif</span>";
            });
            $grid->column('keterangan');
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
        return Show::make($id, new Barang(), function (Show $show) {
            $show->field('id');
            $show->field('nama_barang');
            $show->field('Kategori');
            $show->field('satuan');
            $show->field('jumlah_total');
            $show->field('stok_hari_ini');
            $show->field('dipakai_hari_ini');
            $show->field('status');
            $show->field('keterangan');
            // $show->field('created_at');
            // $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // Custom style khusus halaman form barang
        Admin::style('
        /* Container utama content */
        .content {
            max-width: 1300px;
        }

        /* Card / Box form */
        .box,
        .card {
            max-width: 1050px;
            margin-left: 10px;
            border-radius: 10px;
        }

        /* Padding dalam form */
        .box-body,
        .card-body {
            padding: 30px !important;
        }

        /* Jarak antar field */
        .form-group {
            margin-bottom: 22px;
        }

        /* Input biar lebih clean */
        .form-control {
            border-radius: 6px;
        }

        /* Tombol submit */
        .btn {
            border-radius: 6px;
        }
    ');

        return Form::make(new Barang(), function (Form $form) {

            $form->display('id');

            $form->row(function ($row) {

                $row->width(12)->text('nama_barang', 'Nama Barang')
                    ->required();
            });

            $form->row(function ($row) {

                $row->width(6)->select('Kategori', 'Kategori')
                    ->options([
                        'inti' => 'Inti',
                        'pendukung' => 'Pendukung',
                    ])
                    ->required();

                $row->width(6)->number('jumlah_total', 'Jumlah Total')
                    ->min(0)
                    ->default(0)
                    ->required();
            });

            $form->row(function ($row) {

                $row->width(6)->select('satuan', 'Satuan')
                    ->options([
                        'pcs' => 'Pcs',
                        'unit' => 'Unit',
                        'set' => 'Set',
                        'roll' => 'Roll',
                    ])
                    ->required();
            });

            $form->row(function ($row) {

                $row->width(6)->select('status', 'Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Non-Aktif',
                    ])
                    ->default('aktif')
                    ->required();

                $row->width(6)->text('keterangan', 'Keterangan (Opsional)');
            });

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
