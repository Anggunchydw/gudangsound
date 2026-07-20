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
            $grid->quickSearch(function ($model, $keyword) {
                $model->where('nama_barang', 'like', "%{$keyword}%");
            });
            // $grid->filter(function (Grid\Filter $filter) {

            //     // Popup
            //     $filter->panel();

            //     // Hilangkan filter ID bawaan
            //     $filter->disableIdFilter();

            //     // Cari berdasarkan nama barang
            //     $filter->like('nama_barang', 'Nama Barang');

            //     // Filter kategori
            //     $filter->equal('Kategori', 'Kategori')->select([
            //         'inti' => 'Inti',
            //         'pendukung' => 'Pendukung',
            //     ]);

            //     // Filter status
            //     $filter->equal('status', 'Status')->select([
            //         'aktif' => 'Aktif',
            //         'nonaktif' => 'Nonaktif',
            //     ]);
            // });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    /**
     * Detail Barang
     */
    protected function detail($id)
    {
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

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        return Form::make(new Barang(), function (Form $form) {

            $form->display('id', 'ID');

            /*
        |--------------------------------------------------------------------------
        | Baris 1
        |--------------------------------------------------------------------------
        */

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

            /*
        |--------------------------------------------------------------------------
        | Baris 2
        |--------------------------------------------------------------------------
        */

            $form->row(function ($row) {

                $row->width(6)
                    ->select('Kategori', 'Kategori')
                    ->options([
                        'inti'       => 'Inti',
                        'pendukung'  => 'Pendukung',
                    ])
                    ->required();

                $row->width(6)
                    ->number('jumlah_total', 'Jumlah Total')
                    ->min(0)
                    ->default(0)
                    ->required();
            });

            /*
        |--------------------------------------------------------------------------
        | Baris 3
        |--------------------------------------------------------------------------
        */

            $form->row(function ($row) {

                $row->width(6)
                    ->select('satuan', 'Satuan')
                    ->options([
                        'pcs'  => 'Pcs',
                        'unit' => 'Unit',
                        'set'  => 'Set',
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
        });
    }
}
