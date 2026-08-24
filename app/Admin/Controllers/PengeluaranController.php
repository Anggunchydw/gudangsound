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
use App\Models\CashAccount;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends AdminController
{
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
                ->display(fn($nama) => $nama ?: '-');

            $grid->column('jumlah_pengeluaran')
                ->display(fn($value) => 'Rp ' . number_format($value, 0, ',', '.'));

            $grid->column('kategori')
                ->display(fn($value) => "<span class='badge-kategori'>" . ucfirst($value) . "</span>");

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

            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
            // HILANGKAN SELURUH KOLOM ACTION AGAR TABEL TIDAK KOSONG DI UJUNG KANAN
            $grid->disableActions();
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new Pengeluaran(), function (Show $show) {
            $show->field('id');
            $show->field('tanggal_pengeluaran');
            $show->field('penyewaan.nama_penyewa', 'Penyewaan');
            $show->field('jumlah_pengeluaran');
            $show->field('kategori');
            $show->field('keterangan');
        });
    }

    protected function form()
    {
        return Form::make(new Pengeluaran(), function (Form $form) {
            // Jika ada yang memaksa akses form edit via URL
            if ($form->isEditing()) {
                abort(405, 'Buku besar pengeluaran bersifat immutable dan tidak dapat diubah.');
            }

            $form->disableDeleteButton();
            $form->disableViewButton();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
            $form->disableViewCheck();

            $form->display('id');

            $form->select('penyewaan_id', 'Penyewaan (Opsional)')
                ->options(function () {
                    return Penyewaan::where('status_penyewaan', '!=', 'dibatalkan')
                        ->get()
                        ->mapWithKeys(fn($p) => [$p->id => $p->nama_penyewa]);
                })
                ->help('Pilih penyewaan jika berkaitan dengan acara tertentu. Jika untuk operasional umum, biarkan kosong.');

            $form->currency('jumlah_pengeluaran', 'Jumlah')->symbol('Rp')->required();
            $form->date('tanggal_pengeluaran')->default(now())->required();
            $form->select('kategori')->options([
                'transport'   => 'Transport',
                'perbaikan'   => 'Perbaikan',
                'gaji'        => 'Gaji',
                'operasional' => 'Operasional',
                'lainnya'     => 'Lainnya',
            ])->required();

            $form->textarea('keterangan');

            $form->saving(function (Form $form) {
                if (DB::transactionLevel() === 0) {
                    DB::beginTransaction();
                }

                try {
                    $rawJumlah = request('jumlah_pengeluaran', $form->jumlah_pengeluaran);
                    $jumlah = (float) preg_replace('/[^0-9.]/', '', str_replace(',', '', (string) $rawJumlah));
                    $form->jumlah_pengeluaran = $jumlah;

                    $penyewaanId = request('penyewaan_id', $form->input('penyewaan_id'));

                    if ($form->tanggal_pengeluaran > now()->toDateString()) {
                        DB::rollBack();
                        return $form->response()->error('Tanggal pengeluaran tidak boleh melebihi hari ini.');
                    }

                    if ($jumlah <= 0) {
                        DB::rollBack();
                        return $form->response()->error('Jumlah pengeluaran harus lebih dari 0.');
                    }

                    // Serialization lock pada kas utama
                    CashAccount::lockForUpdate()->find(1)
                        ?? CashAccount::create(['id' => 1, 'name' => 'Kas Utama', 'balance' => 0]);

                    // Validasi sisa dana penyewaan spesifik
                    if (!empty($penyewaanId)) {
                        $penyewaan = Penyewaan::whereKey($penyewaanId)->lockForUpdate()->first();

                        if (!$penyewaan) {
                            DB::rollBack();
                            return $form->response()->error('Penyewaan yang dipilih tidak ditemukan.');
                        }

                        if ($penyewaan->status_penyewaan === 'dibatalkan') {
                            DB::rollBack();
                            return $form->response()->error('Pengeluaran tidak dapat dikaitkan dengan penyewaan yang dibatalkan.');
                        }

                        $totalMasukPenyewaan = (float) Pemasukan::where('penyewaan_id', $penyewaan->id)->sum('jumlah');
                        $totalKeluarPenyewaan = (float) PengeluaranModel::where('penyewaan_id', $penyewaan->id)->sum('jumlah_pengeluaran');
                        $sisaDanaPenyewaan = $totalMasukPenyewaan - $totalKeluarPenyewaan;

                        if ($jumlah > $sisaDanaPenyewaan) {
                            DB::rollBack();
                            return $form->response()->error(
                                'Dana penyewaan tidak mencukupi. Sisa dana penyewaan hanya Rp ' .
                                    number_format(max(0, $sisaDanaPenyewaan), 0, ',', '.')
                            );
                        }
                    }

                    // Validasi kas global perusahaan
                    $totalKasMasuk = (float) Pemasukan::sum('jumlah');
                    $totalKasKeluar = (float) PengeluaranModel::sum('jumlah_pengeluaran');
                    $sisaKasGlobal = $totalKasMasuk - $totalKasKeluar;

                    if ($jumlah > $sisaKasGlobal) {
                        DB::rollBack();
                        return $form->response()->error(
                            'Kas operasional utama tidak mencukupi. Sisa kas global hanya Rp ' .
                                number_format(max(0, $sisaKasGlobal), 0, ',', '.')
                        );
                    }
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return $form->response()->error('Gagal memvalidasi pengeluaran: ' . $e->getMessage());
                }
            });

            $form->saved(function (Form $form) {
                try {
                    if (DB::transactionLevel() > 0) {
                        DB::commit();
                    }
                } catch (\Throwable $e) {
                    if (DB::transactionLevel() > 0) {
                        DB::rollBack();
                    }
                    return $form->response()->error('Gagal menyimpan pengeluaran: ' . $e->getMessage());
                }
            });
        });
    }

    public function destroy($id)
    {
        abort(405, 'Buku besar pengeluaran bersifat immutable dan tidak dapat dihapus.');
    }

    public function update($id)
    {
        abort(405, 'Buku besar pengeluaran bersifat immutable dan tidak dapat diubah.');
    }
}
