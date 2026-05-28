<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Penyewaan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PenyewaanController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Penyewaan(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('nama_penyewa');
            $grid->column('no_tlp');
            $grid->column('tanggal_mulai');
            $grid->column('tanggal_selesai');
            $grid->column('lokasi');
            $grid->column('total_harga');
            $grid->column('status_pembayaran');
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
        return Show::make($id, new Penyewaan(), function (Show $show) {
            $show->field('id');
            $show->field('nama_penyewa');
            $show->field('no_tlp');
            $show->field('tanggal_mulai');
            $show->field('tanggal_selesai');
            $show->field('lokasi');
            $show->field('total_harga');
            $show->field('status_pembayaran');
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
        return Form::make(new Penyewaan(), function (Form $form) {
            $form->display('id');
            $form->text('nama_penyewa');
            $form->text('no_tlp');
            $form->text('tanggal_mulai');
            $form->text('tanggal_selesai');
            $form->text('lokasi');
            $form->text('total_harga');
            $form->text('status_pembayaran');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
