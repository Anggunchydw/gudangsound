<?php

namespace App\Admin\Controllers;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Http\Controllers\AdminController;

class PenggunaController extends AdminController
{
protected function grid()
{
    return Grid::make(new Administrator(), function (Grid $grid) {

        $grid->column('id')->sortable();

        $grid->column('name','Nama');

        $grid->column('username','Username');

        $grid->column('email','Email');

        $grid->column('roles')
            ->display(function ($roles) {
                return collect($roles)
                    ->pluck('name')
                    ->implode(', ');
            });

        $grid->disableViewButton();
    });
}
protected function form()
{
    return Form::make(new Administrator(), function (Form $form) {

        $form->display('id');

        $form->text('name','Nama')
            ->required();

        $form->text('username')
            ->required();

        $form->email('email')
            ->required();

        $form->password('password')
            ->required()
            ->customFormat(function () {
                return '';
            })
            ->saving(function ($value) {

                if (!$value) {
                    return $this->model()->password;
                }

                return bcrypt($value);
            });

        $form->multipleSelect('roles')
            ->options(Role::pluck('name','id'))
            ->required();

    });
}
}
