<?php

namespace App\Admin\Controllers;

use APP\Models\Pemasukan as PemasukanModel;
use App\Admin\Repositories\Pemasukan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AdminController;

class PemasukanController extends AdminController
{
    protected function grid()
    {
        Admin::css(asset('css/pemasukan.css'));
        return Grid::make(new Pemasukan(), function (Grid $grid) {
            $grid->model()
                ->with('penyewaan')
                ->orderByDesc('tanggal_masuk');
            $totalPemasukan = PemasukanModel::sum('jumlah');

            $grid->header("
                <div class='total-pemasukan'>
                    <span class='total-pemasukan-label'>
                        Total Pemasukan :
                    </span>

                    <span class='total-pemasukan-nominal'>
                        Rp " . number_format($totalPemasukan, 0, ',', '.') . "
                    </span>
                </div>
            ");
            $grid->column('id')->sortable();

            $grid->column('tanggal_masuk');
            $grid->column('penyewaan.nama_penyewa', 'Nama Penyewa');
            $grid->column('jenis_pembayaran', 'Jenis')
                ->display(function ($status) {

                    if ($status == 'DP') {
                        return "<span class='status-dp'>DP</span>";
                    }
                    return "<span class='status-lunas'>Lunas</span>";
                });
            $grid->column('jumlah')
                ->display(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                });;
            $grid->column('keterangan');
            $grid->quickSearch(function ($model, $keyword) {
                $model->whereHas('penyewaan', function ($q) use ($keyword) {
                    $q->where('nama_penyewa', 'like', "%{$keyword}%");
                })
                    ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
            $grid->filter(function (Grid\Filter $filter) {

                $filter->equal('jenis_pembayaran')
                    ->select([
                        'DP' => 'DP',
                        'Lunas' => 'Lunas',
                    ]);

                $filter->between('tanggal_masuk')
                    ->date();
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new Pemasukan(), function (Show $show) {
            $show->field('id');
        });
    }


    protected function form()
    {
        return Form::make(new Pemasukan(), function (Form $form) {
            $form->display('id');
        });
    }
}
