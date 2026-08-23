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
        return Form::make(
            new Pengeluaran(),
            function (Form $form) {
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
                        )
                            ->get()
                            ->mapWithKeys(function ($penyewaan) {
                                return [
                                    $penyewaan->id => $penyewaan->nama_penyewa,
                                ];
                            });
                    })
                    ->help('Pilih penyewaan jika pengeluaran berkaitan dengan penyewaan tertentu. Jika tidak, biarkan kosong.');

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

                    $rawJumlah = request('jumlah_pengeluaran', $form->jumlah_pengeluaran);
                    $jumlah = (float) preg_replace('/[^0-9.]/', '', str_replace(',', '', (string) $rawJumlah));
                    $form->jumlah_pengeluaran = $jumlah;

                    $pengeluaranId = $form->getKey();
                    $penyewaanId = request('penyewaan_id', $form->input('penyewaan_id'));

                    // 2. Validasi Tanggal
                    if ($form->tanggal_pengeluaran > now()->toDateString()) {
                        return $form->response()->error('Tanggal pengeluaran tidak boleh melebihi hari ini.');
                    }

                    // 3. Validasi Jumlah
                    if ($jumlah <= 0) {
                        return $form->response()->error('Jumlah pengeluaran harus lebih dari 0.');
                    }

                    // Validasi Dana Penyewaan Spesifik (Jika Terkait Penyewaan)
                    if (!empty($penyewaanId)) {
                        $penyewaan = Penyewaan::find($penyewaanId);

                        if (!$penyewaan) {
                            return $form->response()->error('Penyewaan yang dipilih tidak ditemukan.');
                        }

                        if ($penyewaan->status_penyewaan === 'dibatalkan') {
                            return $form->response()->error('Pengeluaran tidak dapat dikaitkan dengan penyewaan yang dibatalkan.');
                        }

                        // Hitung total pemasukan yang sudah masuk untuk penyewaan ini
                        $totalMasukPenyewaan = (float) Pemasukan::where('penyewaan_id', $penyewaan->id)->sum('jumlah');

                        // Hitung pengeluaran lain untuk penyewaan ini (abaikan record saat ini jika sedang edit)
                        $totalKeluarPenyewaan = (float) PengeluaranModel::where('penyewaan_id', $penyewaan->id)
                            ->when($pengeluaranId, fn($q) => $q->where('id', '!=', $pengeluaranId))
                            ->sum('jumlah_pengeluaran');

                        $sisaDanaPenyewaan = $totalMasukPenyewaan - $totalKeluarPenyewaan;

                        if ($jumlah > $sisaDanaPenyewaan) {
                            return $form->response()->error(
                                "Dana penyewaan tidak mencukupi. Sisa dana penyewaan hanya Rp " .
                                    number_format(max(0, $sisaDanaPenyewaan), 0, ',', '.') .
                                    " (Pengeluaran diajukan: Rp " . number_format($jumlah, 0, ',', '.') . ")"
                            );
                        }
                    }

                    // 5. CONSTRAINT 2: Validasi Kas Global Perusahaan
                    $totalKasMasuk = (float) Pemasukan::sum('jumlah');
                    $totalKasKeluar = (float) PengeluaranModel::when($pengeluaranId, fn($q) => $q->where('id', '!=', $pengeluaranId))
                        ->sum('jumlah_pengeluaran');

                    $sisaKasGlobal = $totalKasMasuk - $totalKasKeluar;

                    if ($jumlah > $sisaKasGlobal) {
                        return $form->response()->error(
                            "Kas operasional utama tidak mencukupi. Sisa kas global hanya Rp " .
                                number_format(max(0, $sisaKasGlobal), 0, ',', '.')
                        );
                    }
                });
            }
        );
    }
}
