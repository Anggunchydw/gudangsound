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
use Carbon\Carbon;
use App\Services\GoogleCalendarService;
use App\Services\TelegramService;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\DetailPenugasan;

class PenugasanController extends AdminController
{

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
            $grid->column('penyewaan.tanggal_mulai', 'Tanggal Acara')
                ->display(function ($value) {
                    return Carbon::parse($value)->format('d-m-Y');
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
            $grid->disableRowSelector();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {

                $filter->like('penyewaan.nama_penyewa', 'Nama Penyewa');

                $filter->like('tim', 'Nama Tim');
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new Penugasan(), function (Show $show) {

            $show->field('id');

            $show->field('penyewaan.nama_penyewa', 'Nama Penyewa');

            $show->field('tim', 'Nama Tim');

            $show->field('created_at', 'Tanggal Dibuat');
        });
    }

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

                            $tanggal = Carbon::parse($item->tanggal_mulai);

                            $label = '';

                            if ($tanggal->isPast() && !$tanggal->isToday()) {
                                $label = '⚠ TERLEWAT | ';
                            } elseif ($tanggal->isToday()) {
                                $label = '🔥 HARI INI | ';
                            }

                            return [
                                $item->id =>
                                $label .
                                    $item->nama_penyewa .
                                    ' | ' .
                                    $tanggal->format('d-m-Y') .
                                    ' | ' .
                                    $item->lokasi,
                            ];
                        });
                })
                ->required();

            $form->text('tim', 'Nama Tim');

            $pegawaiRole = Role::where('slug', 'pegawai')->first();

            $form->checkbox('pegawai', 'Pegawai')
                ->options(function () use ($pegawaiRole) {

                    if (!$pegawaiRole) {
                        return [];
                    }

                    return Administrator::whereHas('roles', function ($q) use ($pegawaiRole) {
                        $q->where('admin_roles.id', $pegawaiRole->id);
                    })
                        ->pluck('name', 'id');
                })
                ->customFormat(function () use ($form) {

                    if (!$form->model()) {
                        return [];
                    }

                    return $form->model()
                        ->pegawai
                        ->pluck('id')
                        ->toArray();
                })
                ->required();
            $form->saving(function (Form $form) {

                // Ambil pegawai yang dipilih
                $pegawai = array_filter((array) $form->input('pegawai'));

                $penyewaanId = $form->input('penyewaan_id');

                // Jika penyewaan atau pegawai belum dipilih,
                // biarkan validasi form yang menangani
                if (!$penyewaanId || empty($pegawai)) {
                    return;
                }

                $penyewaan = Penyewaan::find($penyewaanId);

                if (!$penyewaan) {
                    return;
                }

                $tanggalMulai = Carbon::parse($penyewaan->tanggal_mulai);
                $tanggalSelesai = Carbon::parse($penyewaan->tanggal_selesai);

                // Cari pegawai yang sudah mempunyai penugasan
                // pada tanggal yang beririsan
                $pegawaiTerpakai = DetailPenugasan::whereIn('user_id', $pegawai)
                    ->whereHas('penugasan', function ($q) use (
                        $tanggalMulai,
                        $tanggalSelesai,
                        $form
                    ) {

                        // Jika EDIT, abaikan penugasan yang sedang diedit
                        if ($form->isEditing()) {
                            $q->where('id', '<>', $form->getKey());
                        }

                        $q->whereHas('penyewaan', function ($q) use (
                            $tanggalMulai,
                            $tanggalSelesai
                        ) {

                            // Cek apakah tanggal saling beririsan
                            $q->where(
                                'tanggal_mulai',
                                '<=',
                                $tanggalSelesai->format('Y-m-d')
                            )
                                ->where(
                                    'tanggal_selesai',
                                    '>=',
                                    $tanggalMulai->format('Y-m-d')
                                )
                                ->where(
                                    'status_penyewaan',
                                    '<>',
                                    'dibatalkan'
                                );
                        });
                    })
                    ->with('pegawai')
                    ->get();

                // Jika ada pegawai yang bentrok
                if ($pegawaiTerpakai->isNotEmpty()) {

                    $namaPegawaiBentrok = $pegawaiTerpakai
                        ->map(function ($detail) {
                            return optional($detail->pegawai)->name;
                        })
                        ->filter()
                        ->unique()
                        ->implode(', ');

                    return $form->response()->error(
                        'Pegawai tidak dapat ditugaskan. ' .
                            'Pegawai berikut sudah memiliki penugasan pada tanggal yang beririsan: ' .
                            $namaPegawaiBentrok
                    );
                }
            });

            $form->saved(function (Form $form) {

                // 1. SIMPAN PEGAWAI
                $pegawai = array_filter((array) request('pegawai'));

                $penugasan = Penugasan::find($form->getKey());

                $penugasan->pegawai()->sync($pegawai);

                $penugasan->load([
                    'pegawai',
                    'penyewaan.detailPaket.paket',
                    'penyewaan.detailBarang.barang',
                ]);

                // 2. PERSIAPAN DATA
                $namaPegawai = $penugasan->pegawai
                    ->pluck('name')
                    ->implode(', ');

                $emails = $penugasan->pegawai
                    ->pluck('email')
                    ->filter()
                    ->toArray();

                $deskripsi =
                    "Penyewa : {$penugasan->penyewaan->nama_penyewa}\n" .
                    "Tim : {$penugasan->tim}\n" .
                    "Lokasi : {$penugasan->penyewaan->lokasi}\n" .
                    "Tanggal Mulai : " .
                    date('d F Y', strtotime($penugasan->penyewaan->tanggal_mulai)) . "\n" .
                    "Tanggal Selesai : " .
                    date('d F Y', strtotime($penugasan->penyewaan->tanggal_selesai)) . "\n" .
                    "Pegawai : {$namaPegawai}";

                if ($form->isCreating()) {

                    $judulGoogle = 'Penugasan Baru - ' . $penugasan->penyewaan->nama_penyewa;

                    $judulTelegram = "📢 PENUGASAN BARU";
                } else {

                    $judulGoogle = 'RALAT PENUGASAN - ' . $penugasan->penyewaan->nama_penyewa;

                    $judulTelegram = "⚠️ RALAT PENUGASAN";
                }

                // 3. GOOGLE CALENDAR
                $google = new GoogleCalendarService();
                if ($penugasan->google_event_id) {

                    // Edit event lama
                    $google->updateEvent(
                        $penugasan->google_event_id,
                        $judulGoogle,
                        $penugasan->penyewaan->lokasi,
                        $deskripsi,
                        $penugasan->penyewaan->tanggal_mulai,
                        $penugasan->penyewaan->tanggal_selesai,
                        $emails
                    );
                } else {

                    // Buat event baru
                    $event = $google->createEvent(
                        $judulGoogle,
                        $penugasan->penyewaan->lokasi,
                        $deskripsi,
                        $penugasan->penyewaan->tanggal_mulai,
                        $penugasan->penyewaan->tanggal_selesai,
                        $emails
                    );

                    $penugasan->google_event_id = $event->getId();
                    $penugasan->save();
                }
                // 4. PERSIAPAN PESAN TELEGRAM
                $paket = '';

                foreach ($penugasan->penyewaan->detailPaket as $detail) {

                    if ($detail->paket) {

                        $paket .=
                            "• {$detail->paket->nama_paket} x{$detail->jumlah_paket}\n";
                    }
                }

                $barang = '';

                foreach ($penugasan->penyewaan->detailBarang as $detail) {

                    if ($detail->barang) {

                        $barang .=
                            "• {$detail->barang->nama_barang} x{$detail->jumlah_barang}\n";
                    }
                }

                $pesan =
                    $judulTelegram . "\n\n" .

                    " Penyewa : {$penugasan->penyewaan->nama_penyewa}\n" .
                    " Tim : {$penugasan->tim}\n" .
                    " Lokasi : {$penugasan->penyewaan->lokasi}\n" .
                    " Tanggal : " .
                    date('d-m-Y', strtotime($penugasan->penyewaan->tanggal_mulai)) .
                    " s/d " .
                    date('d-m-Y', strtotime($penugasan->penyewaan->tanggal_selesai)) .
                    "\n\n";

                if ($paket != '') {

                    $pesan .= "📦 Paket\n";
                    $pesan .= $paket . "\n";
                }

                if ($barang != '') {

                    $pesan .= "📦 Barang Satuan\n";
                    $pesan .= $barang . "\n";
                }

                $pesan .=
                    "📝 Keterangan\n" .
                    ($penugasan->penyewaan->keterangan ?: '-') .
                    "\n\n" .

                    "👷 Pegawai Bertugas\n" .
                    $namaPegawai;

                // TELEGRAM
                $telegram = new TelegramService();

                // Pegawai
                foreach ($penugasan->pegawai as $pegawai) {

                    if (!empty($pegawai->telegram_chat_id)) {

                        $telegram->sendMessage(
                            $pegawai->telegram_chat_id,
                            $pesan
                        );
                    }
                }
            });
            Admin::script(<<<'JS'

function loadPegawaiTersedia() {

    var penyewaanId = $('select[name="penyewaan_id"]').val();

    if (!penyewaanId) {
        return;
    }

    var penugasanId = $('input[name="id"]').val() || '';

    $.ajax({
        url: '/admin/penugasan/get-pegawai-tersedia',
        type: 'GET',

        data: {
            penyewaan_id: penyewaanId,
            penugasan_id: penugasanId
        },

        success: function(response) {

            var formGroup = $('input[name="pegawai[]"]')
                .first()
                .closest('.form-group');

            if (!formGroup.length) {
                return;
            }

            var html = '';

            if (response.length === 0) {

                html = `
                    <div style="color:#999; padding:8px 0;">
                        Tidak ada pegawai yang tersedia pada tanggal tersebut.
                    </div>
                `;

            } else {

                response.forEach(function(pegawai) {

                    html += `
                        <label style="display:block; margin-bottom:8px;">
                            <input type="checkbox"
                                   name="pegawai[]"
                                   value="${pegawai.id}">
                            ${pegawai.name}
                        </label>
                    `;

                });
            }

            formGroup.find('.checkbox').first().html(html);
        }
    });
}

$(document).on(
    'change',
    'select[name="penyewaan_id"]',
    function () {

        loadPegawaiTersedia();

    }
);

JS);
        });
    }
    public function pegawaiTersedia()
    {
        $penyewaanId = request('penyewaan_id');
        $penugasanId = request('penugasan_id');

        if (!$penyewaanId) {
            return response()->json([]);
        }

        $penyewaan = Penyewaan::find($penyewaanId);

        if (!$penyewaan) {
            return response()->json([]);
        }

        $tanggalMulai = Carbon::parse($penyewaan->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($penyewaan->tanggal_selesai);

        $pegawaiTerpakai = DetailPenugasan::whereHas(
            'penugasan',
            function ($q) use (
                $tanggalMulai,
                $tanggalSelesai,
                $penugasanId
            ) {

                if ($penugasanId) {
                    $q->where('id', '<>', $penugasanId);
                }

                $q->whereHas('penyewaan', function ($q) use (
                    $tanggalMulai,
                    $tanggalSelesai
                ) {

                    $q->where(
                        'tanggal_mulai',
                        '<=',
                        $tanggalSelesai->format('Y-m-d')
                    )
                        ->where(
                            'tanggal_selesai',
                            '>=',
                            $tanggalMulai->format('Y-m-d')
                        )
                        ->where(
                            'status_penyewaan',
                            '<>',
                            'dibatalkan'
                        );
                });
            }
        )
            ->pluck('user_id')
            ->unique();

        $pegawaiRole = Role::where('slug', 'pegawai')->first();

        if (!$pegawaiRole) {
            return response()->json([]);
        }

        $pegawai = Administrator::whereHas('roles', function ($q) use ($pegawaiRole) {
            $q->where('admin_roles.id', $pegawaiRole->id);
        })
            ->whereNotIn('id', $pegawaiTerpakai)
            ->get(['id', 'name']);

        return response()->json($pegawai);
    }
}
