<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\KondisiBarang;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class KondisiBarangController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new KondisiBarang(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('detail_penyewaan_id');
            $grid->column('kondisi_sebelum');
            $grid->column('kondisi_sesudah');
            $grid->column('catatan');
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
        return Show::make($id, new KondisiBarang(), function (Show $show) {
            $show->field('id');
            $show->field('detail_penyewaan_id');
            $show->field('kondisi_sebelum');
            $show->field('kondisi_sesudah');
            $show->field('catatan');
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
        return Form::make(new KondisiBarang(), function (Form $form) {
            $form->display('id');
            $form->text('detail_penyewaan_id');
            $form->text('kondisi_sebelum');
            $form->text('kondisi_sesudah');
            $form->text('catatan');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
