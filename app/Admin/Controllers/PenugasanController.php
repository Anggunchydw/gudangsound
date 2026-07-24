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
use App\Services\GoogleCalendarService;
use App\Services\TelegramService;
use Dcat\Admin\Http\Controllers\AdminController;

class PenugasanController extends AdminController
{
    /**
     * Hanya Administrator & Admin Operasional
     */
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
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {

                $filter->like('penyewaan.nama_penyewa', 'Nama Penyewa');

                $filter->like('tim', 'Nama Tim');
            });
        });
    }

    /**
     * DETAIL
     */
    protected function detail($id)
    {
        return Show::make($id, new Penugasan(), function (Show $show) {

            $show->field('id');

            $show->field('penyewaan.nama_penyewa', 'Nama Penyewa');

            $show->field('tim', 'Nama Tim');

            $show->field('created_at', 'Tanggal Dibuat');
        });
    }

    /**
     * FORM
     */
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

                    // Jika edit, tampilkan juga penyewaan yang sedang dipakai
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

                            $label = '';

                            if ($item->tanggal_mulai < today()) {
                                $label = '⚠ TERLEWAT | ';
                            } elseif ($item->tanggal_mulai == today()) {
                                $label = '🔥 HARI INI | ';
                            }

                            return [
                                $item->id =>
                                $label .
                                    $item->nama_penyewa .
                                    ' | ' .
                                    date('d-m-Y', strtotime($item->tanggal_mulai)) .
                                    ' | ' .
                                    $item->lokasi,
                            ];
                        });
                })
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

                    if (!$form->model()) {
                        return [];
                    }

                    return $form->model()
                        ->pegawai
                        ->pluck('id')
                        ->toArray();
                })
                ->required();

            $form->saved(function (Form $form) {

                // 1. SIMPAN PEGAWAI
                $pegawai = array_filter(request('pegawai', []));

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

                $emailsPegawai = $penugasan->pegawai
                    ->pluck('email')
                    ->filter()
                    ->toArray();

                $emailsPemilik = Administrator::whereHas('roles', function ($q) {
                    $q->where('slug', 'pemilik');
                })
                    ->pluck('email')
                    ->filter()
                    ->toArray();

                $emailsAdmin = Administrator::whereHas('roles', function ($q) {
                    $q->where('slug', 'admin');
                })
                    ->pluck('email')
                    ->filter()
                    ->toArray();

                $emails = array_unique(array_merge(
                    $emailsPegawai,
                    $emailsPemilik,
                    $emailsAdmin
                ));

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

                // MODE TEST
                $google->createEvent(
                    $judulGoogle,
                    $penugasan->penyewaan->lokasi,
                    $deskripsi,
                    now()->addMinutes(10)->format('Y-m-d\TH:i:s'),
                    now()->addHours(2)->format('Y-m-d\TH:i:s'),
                    $emails
                );


                // MODE PRODUKSI
                /*
                $google->createEvent(
                    $judulGoogle,
                    $penugasan->penyewaan->lokasi,
                    $deskripsi,
                    $penugasan->penyewaan->tanggal_mulai,
                    $penugasan->penyewaan->tanggal_selesai,
                    $emails
                );
                */

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

                    "👤 Penyewa : {$penugasan->penyewaan->nama_penyewa}\n" .
                    "👥 Tim : {$penugasan->tim}\n" .
                    "📍 Lokasi : {$penugasan->penyewaan->lokasi}\n" .
                    "📅 Tanggal : " .
                    date('d-m-Y', strtotime($penugasan->penyewaan->tanggal_mulai)) .
                    " s/d " .
                    date('d-m-Y', strtotime($penugasan->penyewaan->tanggal_selesai)) .
                    "\n\n";

                if ($paket != '') {

                    $pesan .= "📦 Paket\n";
                    $pesan .= $paket . "\n";
                }

                if ($barang != '') {

                    $pesan .= "🎵 Barang Satuan\n";
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

                // Admin
                $admins = Administrator::whereHas('roles', function ($q) {
                    $q->where('slug', 'admin');
                })->get();

                foreach ($admins as $admin) {

                    if (!empty($admin->telegram_chat_id)) {

                        $telegram->sendMessage(
                            $admin->telegram_chat_id,
                            $pesan
                        );
                    }
                }

                // Pemilik
                $pemiliks = Administrator::whereHas('roles', function ($q) {
                    $q->where('slug', 'pemilik');
                })->get();

                foreach ($pemiliks as $pemilik) {

                    if (!empty($pemilik->telegram_chat_id)) {

                        $telegram->sendMessage(
                            $pemilik->telegram_chat_id,
                            $pesan
                        );
                    }
                }
            });
        });
    }
}
