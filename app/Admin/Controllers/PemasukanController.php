<?php

namespace App\Admin\Controllers;

use APP\Models\Pemasukan as PemasukanModel;
use App\Admin\Repositories\Pemasukan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PemasukanController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Pemasukan(), function (Grid $grid) {
            $grid->model()
                ->with('penyewaan')
                ->orderByDesc('id');
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
            $grid->column('jenis_pembayaran')
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

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            // $grid->disableBatchDelete();
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
        return Show::make($id, new Pemasukan(), function (Show $show) {
            $show->field('id');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Pemasukan(), function (Form $form) {
            $form->display('id');
        });
    }
}
