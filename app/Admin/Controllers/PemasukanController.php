<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Pemasukan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PemasukanController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Pemasukan(), function (Grid $grid) {
            $grid->model()->with('penyewaan');
            $grid->column('id')->sortable();

            $grid->column('tanggal_masuk');
            $grid->column('penyewaan.nama_penyewa', 'Nama Penyewa');
            $grid->column('jenis_pembayaran')
                ->display(function ($status) {

                    if ($status == 'DP') {
                        return "<span class='status-dp'>DP</span>";
                    }
                    return "<span class='status-lunas'>Lunas</span>";
                });
            $grid->column('jumlah');
            $grid->column('keterangan');
            // $grid->column('created_at');
            // $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableBatchDelete();
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
        return Show::make($id, new Pemasukan(), function (Show $show) {
            $show->field('id');
            $show->field('penyewaan_id');
            $show->field('tanggal_masuk');
            $show->field('jumlah');
            $show->field('jenis_pembayaran');
            $show->field('keterangan');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Pemasukan(), function (Form $form) {
            $form->display('id');
            $form->text('penyewaan_id');
            $form->text('tanggal_masuk');
            $form->text('jumlah');
            $form->text('jenis_pembayaran');
            $form->text('keterangan');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
