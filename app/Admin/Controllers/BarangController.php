<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Barang;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use App\Models\Barang as BarangModel;
use Dcat\Admin\Http\Controllers\AdminController;

class BarangController extends AdminController

{

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

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableDelete();
            });
            $grid->batchActions(function (Grid\Tools\BatchActions $batch) {
                $batch->disableDelete();
            });
            $grid->quickSearch(function ($model, $keyword) {
                $model->where('nama_barang', 'like', "%{$keyword}%");
            });
        });
    }


    protected function detail($id)
    {
        Admin::css(asset('css/barang.css'));
        $barang = BarangModel::with([
            'kondisiBarang' => function ($q) {

                $q->latest();
            },
            'kondisiBarang.penugasan.penyewaan',
            'kondisiBarang.penugasan.pegawai',
        ])->findOrFail($id);

        return view(
            'admin.barang.detail-barang',
            compact('barang')
        );
    }


    protected function form()
    {
        Admin::css(asset('css/barang.css'));

        return Form::make(new Barang(), function (Form $form) {

            $form->html('<div class="barang-form-wrapper">');

            $form->display('id', 'ID');

            $form->row(function ($row) {

                $row->width(6)
                    ->text('nama_barang', 'Nama Barang')
                    ->required();

                $row->width(6)
                    ->select('status', 'Status')
                    ->options([
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->default('aktif')
                    ->required();
            });


            $form->row(function ($row) {

                $row->width(6)
                    ->select('Kategori', 'Kategori')
                    ->options([
                        'inti' => 'Inti',
                        'pendukung' => 'Pendukung',
                    ])
                    ->required();


                $row->width(6)
                    ->number('jumlah_total', 'Jumlah Total')
                    ->min(0)
                    ->default(0)
                    ->required();
            });


            $form->row(function ($row) {

                $row->width(6)
                    ->select('satuan', 'Satuan')
                    ->options([
                        'pcs' => 'Pcs',
                        'unit' => 'Unit',
                        'set' => 'Set',
                        'roll' => 'Roll',
                    ])
                    ->required();


                $row->width(6)
                    ->textarea('keterangan', 'Keterangan')
                    ->rows(4);
            });


            $form->divider();


            $form->display('created_at', 'Dibuat');
            $form->display('updated_at', 'Diubah');


            $form->html('</div>');
        });
    }
}
