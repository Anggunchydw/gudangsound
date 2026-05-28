<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\DetailPaket;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DetailPaketController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new DetailPaket(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('paket_id');
            $grid->column('barang_id');
            $grid->column('jumlah');
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
        return Show::make($id, new DetailPaket(), function (Show $show) {
            $show->field('id');
            $show->field('paket_id');
            $show->field('barang_id');
            $show->field('jumlah');
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
        return Form::make(new DetailPaket(), function (Form $form) {
            $form->display('id');
            $form->text('paket_id');
            $form->text('barang_id');
            $form->text('jumlah');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
