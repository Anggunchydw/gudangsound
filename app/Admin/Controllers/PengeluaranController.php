<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Pengeluaran;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PengeluaranController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Pengeluaran(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('penyewaan_id');
            $grid->column('jumlah_pengeluaran');
            $grid->column('tanggal_pengeluaran');
            $grid->column('kategori');
            $grid->column('keterangan');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
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
        return Show::make($id, new Pengeluaran(), function (Show $show) {
            $show->field('id');
            $show->field('penyewaan_id');
            $show->field('jumlah_pengeluaran');
            $show->field('tanggal_pengeluaran');
            $show->field('kategori');
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
        return Form::make(new Pengeluaran(), function (Form $form) {
            $form->display('id');
            $form->text('penyewaan_id');
            $form->text('jumlah_pengeluaran');
            $form->text('tanggal_pengeluaran');
            $form->text('kategori');
            $form->text('keterangan');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
