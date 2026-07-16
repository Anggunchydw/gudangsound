<?php

namespace App\Admin\Controllers;

use App\Models\Administrator;
use App\Models\Penugasan;
use App\Models\Penyewaan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PenugasanController extends AdminController
{
    protected function grid()
    {
        return Grid::make(new Penugasan(), function (Grid $grid) {

            $grid->model()
                ->with(['penyewaan', 'pegawai']);

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

            $grid->disableViewButton();

            $grid->filter(function (Grid\Filter $filter) {

                $filter->like('penyewaan.nama_penyewa');

                $filter->like('tim');
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new Penugasan(), function (Show $show) {

            $show->field('id');

            $show->field('penyewaan.nama_penyewa', 'Nama Penyewa');

            $show->field('tim');

            $show->field('created_at');
        });
    }

    protected function form()
    {
        return Form::make(new Penugasan(), function (Form $form) {

            $form->display('id');

            $form->select('penyewaan_id', 'Penyewaan')
                ->options(

                    Penyewaan::whereIn('status_penyewaan', [
                        'booking',
                        'berlangsung'
                    ])
                        ->whereDoesntHave('penugasan')
                        ->orderBy('tanggal_mulai')
                        ->get()
                        ->mapWithKeys(function ($item) {

                            return [
                                $item->id =>
                                $item->nama_penyewa
                                    . ' | '
                                    . date('d-m-Y', strtotime($item->tanggal_mulai))
                                    . ' | '
                                    . $item->lokasi
                            ];
                        })

                )
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

                $form->model()->pegawai()->sync($pegawai);
            });
        });
    }
}
