<?php

namespace App\Admin\Controllers;

use App\Models\Administrator;
use App\Models\Penugasan;
use App\Models\Penyewaan;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PenugasanController extends AdminController
{
    /**
     * Hanya Administrator & Admin Operasional
     */
    protected function authorizeManage()
    {
        $user = Admin::user();

        if (
            ! $user->isRole('administrator') &&
            ! $user->isRole('admin')
        ) {
            abort(403);
        }
    }

    protected function grid()
    {
        return Grid::make(new Penugasan(), function (Grid $grid) {

            $user = Admin::user();

            $grid->model()->with([
                'penyewaan',
                'pegawai'
            ]);

            $grid->column('id')->sortable();

            $grid->column('penyewaan.nama_penyewa', 'Nama Penyewa');

            $grid->column('tim', 'Nama Tim');

            $grid->column('pegawai', 'Pegawai')
                ->display(function () {
                    return $this->pegawai()
                        ->pluck('name')
                        ->implode(', ');
                });
            $grid->column('created_at', 'Tanggal Penugasan')
                ->display(function ($value) {
                    return date('d-m-Y', strtotime($value));
                });

            // Pegawai hanya melihat
            if (
                ! $user->isRole('administrator') &&
                ! $user->isRole('admin')
            ) {

                $grid->disableCreateButton();
                $grid->disableActions();
            }
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {

                $filter->like('penyewaan.nama_penyewa', 'Nama Penyewa');

                $filter->like('tim', 'Nama Tim');
            });
        });
    }

    /**
     * DETAIL
     */
    protected function detail($id)
    {
        return Show::make($id, new Penugasan(), function (Show $show) {

            $show->field('id');

            $show->field('penyewaan.nama_penyewa', 'Nama Penyewa');

            $show->field('tim', 'Nama Tim');

            $show->field('created_at', 'Tanggal Dibuat');
        });
    }

    /**
     * FORM
     */
    protected function form()
    {
        // hanya admin & administrator
        $this->authorizeManage();

        return Form::make(new Penugasan(), function (Form $form) {
            $form->disableDeleteButton();
             $form->disableViewButton();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
            $form->disableViewCheck();
            $form->display('id');

            $form->select('penyewaan_id', 'Penyewaan')
                ->options(function () use ($form) {

                    $query = Penyewaan::where('status_penyewaan', '<>', 'dibatalkan');

                    // Jika edit, tampilkan juga penyewaan yang sedang dipakai
                    if ($form->isEditing()) {

                        $current = $form->model()->penyewaan_id;

                        $query->where(function ($q) use ($current) {
                            $q->whereDoesntHave('penugasan')
                                ->orWhere('id', $current);
                        });
                    } else {

                        $query->whereDoesntHave('penugasan');
                    }

                    return $query
                        ->orderBy('tanggal_mulai')
                        ->get()
                        ->mapWithKeys(function ($item) {

                            $label = '';

                            if ($item->tanggal_mulai < today()) {
                                $label = '⚠ TERLEWAT | ';
                            } elseif ($item->tanggal_mulai == today()) {
                                $label = '🔥 HARI INI | ';
                            }

                            return [
                                $item->id =>
                                $label .
                                    $item->nama_penyewa .
                                    ' | ' .
                                    date('d-m-Y', strtotime($item->tanggal_mulai)) .
                                    ' | ' .
                                    $item->lokasi,
                            ];
                        });
                })
                ->required();

            $form->text('tim', 'Nama Tim')
                ->required();

            $pegawaiRole = Role::where('slug', 'pegawai')->first();

            $form->checkbox('pegawai', 'Pegawai')
                ->options(

                    Administrator::whereHas('roles', function ($q) use ($pegawaiRole) {

                        $q->where('admin_roles.id', $pegawaiRole->id);
                    })->pluck('name', 'id')

                )
                ->customFormat(function () use ($form) {

                    return $form->model()
                        ->pegawai
                        ->pluck('id')
                        ->toArray();
                })
                ->required();

            $form->saved(function (Form $form) {

                $pegawai = request('pegawai', []);

                $form->model()
                    ->pegawai()
                    ->sync($pegawai);
            });
        });
    }
}
