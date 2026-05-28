<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\DetailPenyewaan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DetailPenyewaanController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new DetailPenyewaan(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('penyewaan_id');
            $grid->column('paket_id');
            $grid->column('barang_id');
            $grid->column('jumlah_barang');
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
        return Show::make($id, new DetailPenyewaan(), function (Show $show) {
            $show->field('id');
            $show->field('penyewaan_id');
            $show->field('paket_id');
            $show->field('barang_id');
            $show->field('jumlah_barang');
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
        return Form::make(new DetailPenyewaan(), function (Form $form) {
            $form->display('id');
            $form->text('penyewaan_id');
            $form->text('paket_id');
            $form->text('barang_id');
            $form->text('jumlah_barang');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
