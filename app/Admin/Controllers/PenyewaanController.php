<?php

namespace App\Admin\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Http\Request;

use App\Admin\Repositories\Penyewaan as PenyewaanRepository;
use App\Models\Barang;
use App\Models\Paket;
use App\Models\Penyewaan;
use App\Services\PembayaranService;
use App\Services\PenyewaanService;
use App\Models\Penugasan;
use App\Services\GoogleCalendarService;
use App\Jobs\SyncPenyewaanToGoogleCalendar;
use App\Jobs\SendPenyewaanTelegramNotification;

class PenyewaanController extends AdminController
{
    protected function canManage()
    {
        return Admin::user()->isRole('administrator')
            || Admin::user()->isRole('pemilik');
    }

    protected function canView()
    {
        return Admin::user()->isRole('administrator')
            || Admin::user()->isRole('pemilik')
            || Admin::user()->isRole('admin');
    }

    protected function canPrint()
    {
        return Admin::user()->isRole('administrator')
            || Admin::user()->isRole('pemilik');
    }

    protected function authorizeManage()
    {
        if (! $this->canManage()) {
            abort(403);
        }
    }

    protected function authorizeView()
    {
        if (! $this->canView()) {
            abort(403);
        }
    }

    protected function authorizePrint()
    {
        if (! $this->canPrint()) {
            abort(403);
        }
    }

    protected function grid()
    {
        $this->authorizeView();
        Admin::css(asset('css/penyewaan.css'));
        $canManage = $this->canManage();

        return Grid::make(new PenyewaanRepository(), function (Grid $grid) use ($canManage) {
            $grid->column('id')->sortable();
            $grid->column('nama_penyewa');
            $grid->column('no_tlp');
            $grid->column('tanggal_mulai');
            $grid->column('tanggal_selesai');
            $grid->column('lokasi');
            $grid->column('total_harga')->display(function ($value) {
                return 'Rp ' . number_format($value, 0, ',', '.');
            });
            $grid->column('status_pembayaran', 'Status')->display(function ($value) {
                $class = $value == 'DP' ? 'status-dp' : 'status-lunas';
                return "<span class='{$class}'>{$value}</span>";
            });
            $grid->column('status_badge', 'Status Penyewaan')->display(function ($value) {
                return $value;
            });

            if (! $canManage) {
                $grid->disableCreateButton();
                $grid->disableEditButton();
                $grid->disableDeleteButton();
                $grid->disableBatchDelete();
            }

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('nama_penyewa', 'Nama Penyewa');
                $filter->equal('status_pembayaran')->select([
                    'DP' => 'DP',
                    'Lunas' => 'Lunas'
                ]);
                $filter->between('tanggal_mulai', 'Tanggal Mulai')->date();
            });

            $grid->batchActions(function (Grid\Tools\BatchActions $batch) {
                $batch->disableDelete();
            });

            $grid->disableRowSelector();

            $grid->quickSearch(function ($model, $keyword) {
                $model->where('nama_penyewa', 'like', "%{$keyword}%")
                    ->orWhere('no_tlp', 'like', "%{$keyword}%")
                    ->orWhere('lokasi', 'like', "%{$keyword}%");
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) use ($canManage) {
                $actions->disableDelete();

                if (
                    $canManage &&
                    $actions->row->status_pembayaran == 'DP' &&
                    $actions->row->status_penyewaan != 'dibatalkan'
                ) {
                    $actions->append(
                        '<a href="' . admin_url("penyewaan/{$actions->getKey()}") . '" class="btn btn-sm btn-payment">
                            <i class="feather icon-dollar-sign"></i> Tambah Pembayaran
                        </a>'
                    );
                }

                if (! $canManage) {
                    return;
                }

                $status = $actions->row->status_penyewaan;

                if (in_array($status, ['booking', 'berlangsung'])) {
                    $id = $actions->getKey();
                    $url = admin_url("penyewaan/{$id}/cancel");
                    $token = csrf_token();

                    $actions->append(
                        '<a href="javascript:void(0);"
                            class="btn btn-sm btn-danger btn-block mt-1"
                            onclick="if(confirm(\'Batalkan penyewaan ini?\')) { document.getElementById(\'form-cancel-' . $id . '\').submit(); }">
                            <i class="feather icon-x-circle"></i> Batalkan
                        </a>
                        <form id="form-cancel-' . $id . '" action="' . $url . '" method="POST" style="display:none;">
                            <input type="hidden" name="_token" value="' . $token . '">
                        </form>'
                    );
                }
            });
        });
    }

    protected function detail($id)
    {
        $this->authorizeView();
        $penyewaan = Penyewaan::with([
            'detailBarang.barang',
            'detailPaket.paket.detail.barang',
            'pemasukan' => function ($query) {
                $query->orderBy('tanggal_masuk', 'asc')->orderBy('id', 'asc');
            },
        ])->findOrFail($id);

        return view('admin.penyewaan.detail', compact('penyewaan'));
    }

    protected function form()
    {
        $this->authorizeManage();

        Admin::script(<<<JS
        function formatRupiah(angka){
            if (angka < 0) {
                return '-' + new Intl.NumberFormat('id-ID').format(Math.abs(angka));
            }
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function getCurrency(value){
            value = String(value);
            value = value.replace(/,/g,'');
            return parseFloat(value) || 0;
        }

        function hitungPelunasan(){
            let total = getCurrency($('input[name="total_harga"]').val());
            let dp = getCurrency($('input[name="uang_muka"]').val());
            let sisa = total - dp;
            $('#sisa-pelunasan').text('Rp ' + formatRupiah(sisa));
        }

        $(document).on('keyup change', 'input[name="total_harga"], input[name="uang_muka"]', hitungPelunasan);
        hitungPelunasan();

        $(document).on('change', 'select[name*="[paket_id]"]', function () {
            let row = $(this).closest('tr');
            let jumlah = row.find('input[name*="[jumlah_paket]"]');
            if (!jumlah.val()) {
                jumlah.val(1).trigger('change');
            }
        });

        $(document).on('change', 'select[name*="[barang_id]"]', function () {
            let row = $(this).closest('tr');
            let jumlah = row.find('input[name*="[jumlah_barang]"]');
            if (!jumlah.val()) {
                jumlah.val(1).trigger('change');
            }
        });
        JS);

        return Form::make(PenyewaanRepository::with([
            'detailPaket',
            'detailBarang'
        ]), function (Form $form) {
            $form->disableDeleteButton();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
            $form->disableViewCheck();

            $form->display('id');
            $form->text('nama_penyewa')->required();

            $form->text('no_tlp', 'No. Telepon')
                ->required()
                ->rules([
                    'required',
                    'regex:/^\+?[0-9]\d{7,14}$/'
                ], [
                    'regex' => 'Nomor telepon tidak valid.'
                ]);

            $form->date('tanggal_mulai')->required();
            $form->date('tanggal_selesai')->required();
            $form->text('lokasi')->required();

            // DETAIL PAKET
            $form->divider('Paket');
            $form->hasMany('detailPaket', 'Paket yang Disewa', function (Form\NestedForm $form) {
                $form->select('paket_id', 'Pilih Paket')
                    ->options(Paket::getPaketAktif())
                    ->required();
                $form->number('jumlah_paket', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();

            // DETAIL BARANG
            $form->divider('Barang Satuan');
            $form->hasMany('detailBarang', 'Barang yang Disewa', function (Form\NestedForm $form) {
                $form->select('barang_id', 'Barang')
                    ->options(
                        Barang::where('status', 'aktif')->pluck('nama_barang', 'id')
                    )
                    ->required();
                $form->number('jumlah_barang', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();

            $form->textarea('keterangan', 'Keterangan')->rows(3);
            $form->divider('Pembayaran');
            $form->currency('total_harga', 'Total Harga')->symbol('Rp')->required();

            if ($form->isCreating()) {
                $form->currency('uang_muka', 'Uang Muka (DP)')->symbol('Rp')->required();
            } else {
                $form->display('uang_muka', 'Total Sudah Dibayar')
                    ->with(function ($value) {
                        return 'Rp ' . number_format($value, 0, ',', '.');
                    });
            }

            $form->html('
            <div class="form-group">
                <label class="control-label">Sisa Pelunasan</label>
                <div>
                    <strong id="sisa-pelunasan" style="color:#dc3545;">Rp 0</strong>
                </div>
            </div>
            ');

            $form->hidden('status_pembayaran');
            $form->hidden('status_penyewaan')->default('booking');

            // 1. SAVING: Buka Transaksi & Validasi Bisnis (Termasuk Locking Stok)
            $form->saving(function (Form $form) {
                try {
                    PenyewaanService::validate($form);
                } catch (\Throwable $e) {
                    PenyewaanService::rollbackTransaction();
                    return $form->response()->error($e->getMessage());
                }
            });

            // 2. SAVED: Simpan DP Awal, Commit Transaksi, dan Dispatch Background Jobs
            // 2. SAVED: Simpan DP Awal, Commit Transaksi, dan Dispatch Background Jobs
            $form->saved(function (Form $form) {
                $isCreating = $form->isCreating();

                // Tahap 1: Transaksi Utama Bisnis (Penyewaan + DP Awal)
                try {
                    if ($isCreating) {
                        PenyewaanService::buatPembayaranAwal($form->getKey());
                    }

                    PenyewaanService::commitTransaction();
                } catch (\Throwable $e) {
                    PenyewaanService::rollbackTransaction();
                    return $form->response()->error('Gagal memproses transaksi: ' . $e->getMessage());
                }

                $penyewaan = Penyewaan::find($form->getKey());
                if (! $penyewaan) {
                    return $form->response()->success('Penyewaan berhasil disimpan.')->redirect(admin_url('penyewaan'));
                }

                // Tahap 2: Integrasi Eksternal / Dispatch Queue Job (Fault-Tolerant)
                $pesanSukses = 'Penyewaan berhasil disimpan.';

                try {
                    $penyewaan->update([
                        'calendar_sync_status' => 'pending',
                        'notification_status' => 'pending',
                    ]);

                    SyncPenyewaanToGoogleCalendar::dispatch($penyewaan->id, $isCreating);
                    SendPenyewaanTelegramNotification::dispatch($penyewaan->id, $isCreating);
                } catch (\Throwable $e) {
                   
                    \Illuminate\Support\Facades\Log::error('Queue Dispatch Failed (Penyewaan ID ' . $penyewaan->id . '): ' . $e->getMessage());

                    $penyewaan->update([
                        'calendar_sync_status' => 'failed',
                        'notification_status' => 'failed',
                    ]);

                    $pesanSukses = 'Penyewaan berhasil disimpan. Antrean sinkronisasi kalender/notifikasi tertunda.';
                }

                return $form->response()
                    ->success($pesanSukses)
                    ->redirect(admin_url('penyewaan/' . $form->getKey()));
            });
        });
    }

    public function simpanPembayaran(Request $request, $id)
    {
        $this->authorizeManage();
        $request->validate([
            'nominal' => 'required|numeric|min:1'
        ]);

        $penyewaan = Penyewaan::findOrFail($id);

        if ($penyewaan->status_penyewaan == 'dibatalkan') {
            admin_error('Gagal', 'Pembayaran tidak dapat dilakukan karena penyewaan sudah dibatalkan.');
            return redirect(admin_url("penyewaan/{$penyewaan->id}"));
        }

        try {
            PembayaranService::tambahPembayaran($penyewaan, (float) $request->nominal);
        } catch (\Exception $e) {
            return back()->withErrors(['nominal' => $e->getMessage()]);
        }

        admin_success('Berhasil', 'Pembayaran berhasil ditambahkan.');
        return redirect(admin_url("penyewaan/{$penyewaan->id}"));
    }

    public function cetak($id)
    {
        $this->authorizePrint();

        $penyewaan = Penyewaan::with([
            'detailBarang.barang',
            'detailPaket.paket.detail.barang',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.penyewaan.invoice', compact('penyewaan'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Bukti-Penyewaan-' . $penyewaan->id . '.pdf');
    }

    public function cancel($id)
    {
        $this->authorizeManage();

        $penyewaan = Penyewaan::findOrFail($id);

        if (!in_array($penyewaan->status_penyewaan, ['booking', 'berlangsung'])) {
            admin_error('Gagal', 'Penyewaan tidak dapat dibatalkan.');
            return redirect(admin_url('penyewaan'));
        }

        $penyewaan->update(['status_penyewaan' => 'dibatalkan']);

        $penugasan = Penugasan::where('penyewaan_id', $penyewaan->id)->first();
        if ($penugasan && $penugasan->google_event_id) {
            try {
                app(GoogleCalendarService::class)->deleteEvent($penugasan->google_event_id);
            } catch (\Throwable $th) {
            }
            $penugasan->google_event_id = null;
            $penugasan->save();
        }

        admin_success('Berhasil', 'Penyewaan berhasil dibatalkan.');
        return redirect(admin_url('penyewaan'));
    }
}
