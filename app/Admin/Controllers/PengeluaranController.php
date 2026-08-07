<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Pengeluaran as PengeluaranModel;
use App\Models\Penyewaan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Dcat\Admin\Admin;

class PengeluaranController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Admin::css(asset('css/pemasukan.css'));
        return Grid::make(new Pengeluaran(), function (Grid $grid) {
            $grid->model()
                ->with('penyewaan')
                ->orderByDesc('tanggal_pengeluaran')
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
                });

            $grid->column('kategori')
                ->display(function ($value) {
                    return "<span class='badge-kategori'>" . ucfirst($value) . "</span>";
                });

            $grid->column('keterangan');

            $grid->filter(function ($filter) {
                $filter->equal('kategori')->select([
                    'transport'   => 'Transport',
                    'perbaikan'   => 'Perbaikan',
                    'gaji'        => 'Gaji',
                    'operasional' => 'Operasional',
                    'lainnya'     => 'Lainnya',
                ]);

                $filter->between('tanggal_pengeluaran')->date();
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
            $form->disableDeleteButton();
            $form->disableViewButton();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
            $form->disableViewCheck();
            $form->display('id');

            $form->select('penyewaan_id', 'Penyewaan (Opsional)')
                ->options(function () {

                    return Penyewaan::where(
                        'status_penyewaan',
                        '!=',
                        'dibatalkan'
                    )->get()
                        ->map(function ($penyewaan) {

                            $totalPemasukan = Pemasukan::where(
                                'penyewaan_id',
                                $penyewaan->id
                            )->sum('jumlah');

                            $totalPengeluaran = PengeluaranModel::where(
                                'penyewaan_id',
                                $penyewaan->id
                            )->sum('jumlah_pengeluaran');

                            $sisaDana = $totalPemasukan - $totalPengeluaran;

                            if ($sisaDana <= 0) {
                                return null;
                            }

                            return [
                                'id'    => $penyewaan->id,
                                'label' => $penyewaan->nama_penyewa .
                                    ' - Sisa Dana: Rp ' .
                                    number_format($sisaDana, 0, ',', '.')
                            ];
                        })
                        ->filter()
                        ->mapWithKeys(function ($item) {
                            return [
                                $item['id'] => $item['label'],
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
                    'transport'   => 'Transport',
                    'perbaikan'   => 'Perbaikan',
                    'gaji'        => 'Gaji',
                    'operasional' => 'Operasional',
                    'lainnya'     => 'Lainnya',
                ])
                ->required();

            $form->textarea('keterangan');

            $form->saving(function (Form $form) {

                $jumlah = (float) str_replace(',', '', $form->jumlah_pengeluaran);

                $form->jumlah_pengeluaran = $jumlah;

                if ($form->tanggal_pengeluaran > now()->toDateString()) {
                    return $form->response()->error(
                        'Tanggal pengeluaran tidak boleh melebihi hari ini.'
                    );
                }

                if ($jumlah <= 0) {
                    return $form->response()->error(
                        'Jumlah pengeluaran harus lebih dari 0.'
                    );
                }

                if ($form->penyewaan_id) {

                    // Total pemasukan penyewaan
                    $totalPemasukan = Pemasukan::where(
                        'penyewaan_id',
                        $form->penyewaan_id
                    )->sum('jumlah');

                    $totalPengeluaran = PengeluaranModel::where(
                        'penyewaan_id',
                        $form->penyewaan_id
                    )
                        ->where('id', '!=', $form->getKey())
                        ->sum('jumlah_pengeluaran');

                    $totalSetelahTambah = $totalPengeluaran + $jumlah;

                    if ($totalSetelahTambah > $totalPemasukan) {

                        $sisa = $totalPemasukan - $totalPengeluaran;

                        return $form->response()->error(
                            "Pengeluaran melebihi pemasukan penyewaan. " .
                                "Sisa dana hanya Rp " .
                                number_format(max($sisa, 0), 0, ',', '.')
                        );
                    }
                } else {

                    $totalPemasukan = Pemasukan::sum('jumlah');

                    $totalPengeluaran = PengeluaranModel::where(
                        'id',
                        '!=',
                        $form->getKey()
                    )->sum('jumlah_pengeluaran');

                    $sisaKas = $totalPemasukan - $totalPengeluaran;

                    if ($jumlah > $sisaKas) {
                        return $form->response()->error(
                            "Kas perusahaan tidak mencukupi. Sisa kas hanya Rp " .
                                number_format(max($sisaKas, 0), 0, ',', '.')
                        );
                    }
                }
            });
        });
    }
}
