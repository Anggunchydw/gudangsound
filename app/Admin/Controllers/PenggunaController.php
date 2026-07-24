<?php

namespace App\Admin\Controllers;

use Illuminate\Validation\Rule;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\Administrator;
use Dcat\Admin\Models\Role;

class PenggunaController extends AdminController
{
    protected function grid()
    {
        return Grid::make(new Administrator(), function (Grid $grid) {

            $currentUser = Admin::user();

            // Selain Super Admin, sembunyikan akun Administrator
            if (! $currentUser->isRole('administrator')) {

                $grid->model()->whereDoesntHave('roles', function ($query) {
                    $query->where('slug', 'administrator');
                });
            }

            $grid->column('id')->sortable();

            $grid->column('name', 'Nama');

            $grid->column('username', 'Username');

            $grid->column('email', 'Email');
            $grid->column('telegram_chat_id', 'Telegram Chat ID');

            $grid->column('roles')
                ->display(function ($roles) {

                    return collect($roles)
                        ->pluck('name')
                        ->implode(', ');
                });

            $grid->disableViewButton();
        });
    }

    protected function detail($id)
    {
        abort(404);
    }

    protected function form()
    {
        return Form::make(new Administrator(), function (Form $form) {

            $form->editing(function (Form $form) {

                $currentUser = Admin::user();

                $user = $form->model();

                if (
                    $user &&
                    $user->isRole('administrator') &&
                    ! $currentUser->isRole('administrator')
                ) {
                    abort(403);
                }
            });

            $form->saving(function (Form $form) {

                // Password kosong = jangan diubah
                if (empty(request('password'))) {
                    $form->deleteInput('password');
                } else {
                    $form->password = bcrypt(request('password'));
                }
                $currentUser = Admin::user();

                $user = $form->model();

                // Tidak boleh mengedit akun Administrator
                if (
                    $user &&
                    $user->exists &&
                    $user->isRole('administrator') &&
                    ! $currentUser->isRole('administrator')
                ) {
                    abort(403);
                }

                // Tidak boleh memberikan role Administrator
                if (! $currentUser->isRole('administrator')) {

                    $adminRoleId = Role::where('slug', 'administrator')->value('id');

                    $selectedRoles = request('roles', []);

                    if (in_array($adminRoleId, $selectedRoles)) {
                        abort(403);
                    }
                }
            });

            $form->display('id');

            $form->text('name', 'Nama')
                ->required();

            $form->text('username', 'Username')
                ->required()
                ->creationRules(['required', 'unique:admin_users,username'])
                ->updateRules([
                    'required',
                    Rule::unique('admin_users', 'username')->ignore($form->model()->id),
                ]);

            $form->email('email', 'Email')
                ->required()
                ->creationRules(['required', 'email', 'unique:admin_users,email'])
                ->updateRules([
                    'required',
                    'email',
                    Rule::unique('admin_users', 'email')->ignore($form->model()->id),
                ]);
            $form->text('telegram_chat_id', 'Telegram Chat ID');

            $password = $form->password('password', 'Password')
                ->customFormat(function () {
                    return '';
                });

            if ($form->isCreating()) {
                $password->required();
            }

            $currentUser = Admin::user();

            $roles = Role::query();

            // Selain Administrator, role Administrator tidak ditampilkan
            if (! $currentUser->isRole('administrator')) {

                $roles->where('slug', '<>', 'administrator');
            }

            $form->multipleSelect('roles', 'Role')
                ->options($roles->pluck('name', 'id'))
                ->required();
        });
    }
}
