<?php

namespace App\Admin\Controllers;


use App\Models\Pemasukan;
use App\Models\Pengeluaran as PengeluaranModel;
use App\Models\Penyewaan;
use App\Admin\Repositories\Pengeluaran;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PengeluaranController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Pengeluaran(), function (Grid $grid) {
            $grid->model()
                ->with('penyewaan')
                ->orderByDesc('id');
            $totalPengeluaran = PengeluaranModel::sum('jumlah_pengeluaran');

            $grid->header("
            <div class='total-pemasukan'>
                <span class='total-pemasukan-label'>
                    Total Pengeluaran :
                </span>

                <span class='total-pemasukan-nominal'>
                    Rp " . number_format($totalPengeluaran, 0, ',', '.') . "
                </span>
            </div>
        ");

            $grid->column('id')->sortable();
            $grid->column('tanggal_pengeluaran');
            $grid->column('penyewaan.nama_penyewa', 'Penyewaan')
                ->display(function ($nama) {
                    return $nama ?: '-';
                });
            $grid->column('jumlah_pengeluaran')
                ->display(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                });;

            $grid->column('kategori')
                ->display(function ($value) {
                    return "<span class='badge-kategori'>" . ucfirst($value) . "</span>";
                });
            $grid->column('keterangan');


            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
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
        return Show::make($id, new Pengeluaran(), function (Show $show) {
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
        return Form::make(new Pengeluaran(), function (Form $form) {
            $form->display('id');
            $form->select('penyewaan_id', 'Penyewaan (Opsional)')
                ->options(function () {

                    return Penyewaan::all()->mapWithKeys(function ($penyewaan) {

                        $totalPemasukan = Pemasukan::where(
                            'penyewaan_id',
                            $penyewaan->id
                        )->sum('jumlah');

                        $totalPengeluaran = PengeluaranModel::where(
                            'penyewaan_id',
                            $penyewaan->id
                        )->sum('jumlah_pengeluaran');

                        $sisaDana = $totalPemasukan - $totalPengeluaran;

                        return [
                            $penyewaan->id =>
                            $penyewaan->nama_penyewa .
                                ' - Sisa Dana: Rp ' .
                                number_format($sisaDana, 0, ',', '.')
                        ];
                    });
                })
                ->help('Kosongkan jika bukan berasal dari penyewaan.');
            $form->currency('jumlah_pengeluaran', 'Jumlah')
                ->symbol('Rp')
                ->required();

            $form->date('tanggal_pengeluaran')
                ->default(now())
                ->required();

            $form->select('kategori')
                ->options([
                    'transport'  => 'Transport',
                    'perbaikan'  => 'Perbaikan',
                    'gaji'       => 'Gaji',
                    'operasional' => 'Operasional',
                    'lainnya'    => 'Lainnya',
                ]);

            $form->textarea('keterangan');

            $form->display('created_at');
            $form->display('updated_at');

            $form->saving(function (Form $form) {

                $jumlah = (float) str_replace(',', '', $form->jumlah_pengeluaran);

                $form->jumlah_pengeluaran = $jumlah;

                if ($jumlah <= 0) {
                    return $form->response()->error(
                        'Jumlah pengeluaran harus lebih dari 0.'
                    );
                }
                if ($form->penyewaan_id) {

                    // Total uang masuk dari penyewaan
                    $totalPemasukan = Pemasukan::where(
                        'penyewaan_id',
                        $form->penyewaan_id
                    )->sum('jumlah');

                    // Total pengeluaran lama
                    $totalPengeluaran = PengeluaranModel::where(
                        'penyewaan_id',
                        $form->penyewaan_id
                    )
                        // supaya saat edit tidak menghitung dirinya sendiri
                        ->where('id', '!=', $form->getKey())
                        ->sum('jumlah_pengeluaran');

                    // Setelah ditambah pengeluaran baru
                    $totalSetelahTambah = $totalPengeluaran + $jumlah;

                    if ($totalSetelahTambah > $totalPemasukan) {

                        $sisa = $totalPemasukan - $totalPengeluaran;

                        return $form->response()->error(
                            "Pengeluaran melebihi pemasukan penyewaan. "
                                . "Sisa dana hanya Rp " . number_format(max($sisa, 0), 0, ',', '.')
                        );
                    }
                }
            });
        });
    }
}
