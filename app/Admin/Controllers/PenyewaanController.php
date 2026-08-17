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
use App\Models\Administrator;
use App\Services\TelegramService;


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
            $grid->column('total_harga')
                ->display(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                });
            $grid->column('status_pembayaran', 'Status')
                ->display(function ($value) {

                    $class = $value == 'DP'
                        ? 'status-dp'
                        : 'status-lunas';

                    return "<span class='{$class}'>{$value}</span>";
                });
            $grid->column('status_badge', 'Status Penyewaan')
                ->display(function ($value) {
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

                $filter->equal('status_pembayaran')
                    ->select([
                        'DP' => 'DP',
                        'Lunas' => 'Lunas'
                    ]);

                $filter->between('tanggal_mulai', 'Tanggal Mulai')
                    ->date();
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

                    $url = admin_url("penyewaan/{$actions->getKey()}/cancel");

                    $actions->append(
                        "<a class='btn btn-sm btn-danger'
                        href='{$url}'
                        onclick=\"return confirm('Batalkan penyewaan ini?')\">
                        <i class='feather icon-x-circle'></i> Batalkan
                        </a>"
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
                $query->orderBy('tanggal_masuk', 'asc')
                    ->orderBy('id', 'asc');
            },
        ])->findOrFail($id);
        return view(
            'admin.penyewaan.detail',
            compact('penyewaan')
        );
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

            let total = getCurrency(
                $('input[name="total_harga"]').val()
            );

            let dp = getCurrency(
                $('input[name="uang_muka"]').val()
            );

            let sisa = total - dp;

            $('#sisa-pelunasan').text(
                'Rp ' + formatRupiah(sisa)
            );
        }

        $(document).on(
            'keyup change',
            'input[name="total_harga"], input[name="uang_muka"]',
            hitungPelunasan
        );

        hitungPelunasan();

        $(document).on('change', 'select[name*="[paket_id]"]', function () {

            let row = $(this).closest('tr');
            let jumlah = row.find('input[name*="[jumlah_paket]"]');

            if (!jumlah.val()) {
                jumlah.val(1).trigger('change');
            }
        });

        // Barang
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
            $form->text('nama_penyewa')
                ->required();

            $form->text('no_tlp', 'No. Telepon')
                ->required()
                ->rules([
                    'required',
                    'regex:/^\+?[0-9]\d{7,14}$/'
                ], [
                    'regex' => 'Nomor telepon tidak valid.'
                ]);

            $form->date('tanggal_mulai')
                ->required();

            $form->date('tanggal_selesai')
                ->required();

            $form->text('lokasi')
                ->required();

            //DETAIL PAKET
            $form->divider('Paket');

            $form->hasMany('detailPaket', 'Paket yang Disewa', function (Form\NestedForm $form) {

                $form->select('paket_id', 'Pilih Paket')
                    ->options(
                        Paket::getPaketAktif()
                    )
                    ->required();

                $form->number('jumlah_paket', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();

            //DETAIL BARANG
            $form->divider('Barang Satuan');

            $form->hasMany('detailBarang', 'Barang yang Disewa', function (Form\NestedForm $form) {

                $form->select('barang_id', 'Barang')
                    ->options(
                        Barang::where('status', 'aktif')
                            ->pluck('nama_barang', 'id')
                    )
                    ->required();

                $form->number('jumlah_barang', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();
            $form->textarea('keterangan', 'Keterangan')
                ->rows(3);
            $form->divider('Pembayaran');
            $form->currency('total_harga', 'Total Harga')
                ->symbol('Rp')
                ->required();
            if ($form->isCreating()) {

                $form->currency('uang_muka', 'Uang Muka (DP)')
                    ->symbol('Rp')
                    ->required();
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
                    <strong id="sisa-pelunasan" style="color:#dc3545;">
                        Rp 0
                    </strong>
                </div>
            </div>
            ');
            $form->hidden('status_pembayaran');
            $form->hidden('status_penyewaan')->default('booking');

            $form->saving(function (Form $form) {

                try {

                    PenyewaanService::validate($form);
                } catch (\Exception $e) {

                    return $form->response()->error(
                        $e->getMessage()
                    );
                }
            });

            $form->saved(function (Form $form) {

                if ($form->isCreating()) {
                    PenyewaanService::buatPembayaranAwal($form->getKey());
                }

                $penyewaan = Penyewaan::find($form->getKey());

                $penyewaan->load([
                    'detailPaket.paket',
                    'detailBarang.barang',
                    'penugasan.pegawai',
                ]);

                // GOOGLE CALENDAR PENYEWAAN
                $emails = Administrator::whereHas('roles', function ($q) {
                    $q->whereIn('slug', ['admin', 'pemilik']);
                })
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();

                $calendar = new GoogleCalendarService();

                $judulGoogle = $form->isCreating()
                    ? 'Penyewaan Baru - ' . $penyewaan->nama_penyewa
                    : 'Ralat Penyewaan - ' . $penyewaan->nama_penyewa;

                if ($penyewaan->google_event_id) {

                    $calendar->updateEvent(
                        $penyewaan->google_event_id,
                        $judulGoogle,
                        $penyewaan->lokasi,
                        $penyewaan->keterangan ?? '-',
                        $penyewaan->tanggal_mulai,
                        $penyewaan->tanggal_selesai,
                        $emails
                    );
                } else {

                    $event = $calendar->createEvent(
                        $judulGoogle,
                        $penyewaan->lokasi,
                        $penyewaan->keterangan ?? '-',
                        $penyewaan->tanggal_mulai,
                        $penyewaan->tanggal_selesai,
                        $emails
                    );

                    $penyewaan->google_event_id = $event->getId();
                    $penyewaan->save();
                }


                // UPDATE GOOGLE CALENDAR PENUGASAN

                if ($penyewaan->penugasan) {
                    $penugasan = $penyewaan->penugasan;

                    $pegawaiEmails = $penugasan->pegawai
                        ->pluck('email')
                        ->filter()
                        ->toArray();


                    $namaPegawai = $penugasan->pegawai
                        ->pluck('name')
                        ->implode(', ');


                    $deskripsiPenugasan =
                        "Penyewa : {$penyewaan->nama_penyewa}\n" .
                        "Tim : {$penugasan->tim}\n" .
                        "Lokasi : {$penyewaan->lokasi}\n" .
                        "Tanggal : " .
                        date('d F Y', strtotime($penyewaan->tanggal_mulai)) .
                        " s/d " .
                        date('d F Y', strtotime($penyewaan->tanggal_selesai)) .
                        "\nPegawai : {$namaPegawai}";


                    $calendar = new GoogleCalendarService();


                    if ($penugasan->google_event_id) {

                        // update event lama
                        $calendar->updateEvent(
                            $penugasan->google_event_id,
                            "Penugasan - {$penyewaan->nama_penyewa}",
                            $penyewaan->lokasi,
                            $deskripsiPenugasan,
                            $penyewaan->tanggal_mulai,
                            $penyewaan->tanggal_selesai,
                            $pegawaiEmails
                        );
                    } else {

                        // jika belum ada event buat baru
                        $event = $calendar->createEvent(
                            "Penugasan - {$penyewaan->nama_penyewa}",
                            $penyewaan->lokasi,
                            $deskripsiPenugasan,
                            $penyewaan->tanggal_mulai,
                            $penyewaan->tanggal_selesai,
                            $pegawaiEmails
                        );


                        $penugasan->google_event_id = $event->getId();
                        $penugasan->save();
                    }
                }

                // TELEGRAM
                $judulTelegram = $form->isCreating()
                    ? "📅 PENYEWAAN BARU"
                    : "⚠️ RALAT PENYEWAAN";

                $paket = '';

                foreach ($penyewaan->detailPaket as $detail) {

                    if ($detail->paket) {

                        $paket .=
                            "• {$detail->paket->nama_paket} x{$detail->jumlah_paket}\n";
                    }
                }

                $barang = '';

                foreach ($penyewaan->detailBarang as $detail) {

                    if ($detail->barang) {

                        $barang .=
                            "• {$detail->barang->nama_barang} x{$detail->jumlah_barang}\n";
                    }
                }

                $telegram = new TelegramService();

                $pesan =
                    $judulTelegram . "\n\n" .

                    " Penyewa : {$penyewaan->nama_penyewa}\n" .
                    " No. HP : {$penyewaan->no_tlp}\n" .
                    " Lokasi : {$penyewaan->lokasi}\n" .
                    " Tanggal : " .
                    date('d-m-Y', strtotime($penyewaan->tanggal_mulai)) .
                    " s/d " .
                    date('d-m-Y', strtotime($penyewaan->tanggal_selesai)) .
                    "\n\n";

                if ($paket != '') {

                    $pesan .=
                        "📦 Paket\n" .
                        $paket .
                        "\n";
                }

                if ($barang != '') {

                    $pesan .=
                        "📦 Barang Satuan\n" .
                        $barang .
                        "\n";
                }

                $pesan .=
                    "📝 Keterangan\n" .
                    ($penyewaan->keterangan ?: "-") .
                    "\n\n" .
                    "💳 Status Pembayaran : {$penyewaan->status_pembayaran}";

                $admins = Administrator::whereHas('roles', function ($q) {
                    $q->whereIn('slug', ['admin', 'pemilik']);
                })->get();

                foreach ($admins as $admin) {

                    if ($admin->telegram_chat_id) {

                        $telegram->sendMessage(
                            $admin->telegram_chat_id,
                            $pesan
                        );
                    }
                }
                // TELEGRAM RALAT PENUGASAN KE PEGAWAI

                if (
                    !$form->isCreating() &&
                    $penyewaan->penugasan
                ) {

                    $penugasan = $penyewaan->penugasan;

                    $namaPegawai = $penugasan->pegawai
                        ->pluck('name')
                        ->implode(', ');


                    $paket = '';

                    foreach ($penyewaan->detailPaket as $detail) {

                        if ($detail->paket) {

                            $paket .=
                                "• {$detail->paket->nama_paket} x{$detail->jumlah_paket}\n";
                        }
                    }


                    $barang = '';

                    foreach ($penyewaan->detailBarang as $detail) {

                        if ($detail->barang) {

                            $barang .=
                                "• {$detail->barang->nama_barang} x{$detail->jumlah_barang}\n";
                        }
                    }


                    $pesanRalat =
                        "⚠️ RALAT PENUGASAN\n\n" .

                        " Penyewa : {$penyewaan->nama_penyewa}\n" .
                        " Tim : {$penugasan->tim}\n" .
                        " Lokasi : {$penyewaan->lokasi}\n" .
                        " Tanggal : " .
                        date('d-m-Y', strtotime($penyewaan->tanggal_mulai)) .
                        " s/d " .
                        date('d-m-Y', strtotime($penyewaan->tanggal_selesai)) .
                        "\n\n";


                    if ($paket != '') {

                        $pesanRalat .=
                            "📦 Paket\n" .
                            $paket .
                            "\n";
                    }


                    if ($barang != '') {

                        $pesanRalat .=
                            "📦 Barang Satuan\n" .
                            $barang .
                            "\n";
                    }


                    $pesanRalat .=
                        "📝 Keterangan\n" .
                        ($penyewaan->keterangan ?: '-') .
                        "\n\n" .

                        "👷 Pegawai Bertugas\n" .
                        $namaPegawai;


                    $telegram = new TelegramService();


                    foreach ($penugasan->pegawai as $pegawai) {

                        if ($pegawai->telegram_chat_id) {

                            $telegram->sendMessage(
                                $pegawai->telegram_chat_id,
                                $pesanRalat
                            );
                        }
                    }
                }
                return $form->response()
                    ->success('Penyewaan berhasil disimpan.')
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

            admin_error(
                'Gagal',
                'Pembayaran tidak dapat dilakukan karena penyewaan sudah dibatalkan.'
            );

            return redirect(
                admin_url("penyewaan/{$penyewaan->id}")
            );
        }

        try {

            PembayaranService::tambahPembayaran(
                $penyewaan,
                (float) $request->nominal
            );
        } catch (\Exception $e) {

            return back()->withErrors([
                'nominal' => $e->getMessage()
            ]);
        }

        admin_success(
            'Berhasil',
            'Pembayaran berhasil ditambahkan.'
        );

        return redirect(
            admin_url("penyewaan/{$penyewaan->id}")
        );
    }

    public function cetak($id)
    {
        $this->authorizePrint();

        $penyewaan = Penyewaan::with([
            'detailBarang.barang',
            'detailPaket.paket.detail.barang',
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'admin.penyewaan.invoice',
            compact('penyewaan')
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Bukti-Penyewaan-' . $penyewaan->id . '.pdf'
        );
    }

    public function cancel($id)
    {
        $this->authorizeManage();

        $penyewaan = Penyewaan::findOrFail($id);

        // Hanya penyewaan dengan status booking atau berlangsung
        // yang boleh dibatalkan
        if (!in_array($penyewaan->status_penyewaan, ['booking', 'berlangsung'])) {

            admin_error(
                'Gagal',
                'Penyewaan tidak dapat dibatalkan.'
            );

            return redirect(admin_url('penyewaan'));
        }

        // Ubah status penyewaan menjadi dibatalkan
        $penyewaan->update([
            'status_penyewaan' => 'dibatalkan'
        ]);

        // Hapus event Google Calendar penugasan
        $penugasan = Penugasan::where(
            'penyewaan_id',
            $penyewaan->id
        )->first();

        if ($penugasan && $penugasan->google_event_id) {

            app(GoogleCalendarService::class)
                ->deleteEvent($penugasan->google_event_id);

            $penugasan->google_event_id = null;
            $penugasan->save();
        }

        admin_success(
            'Berhasil',
            'Penyewaan berhasil dibatalkan.'
        );

        return redirect(admin_url('penyewaan'));
    }
}
