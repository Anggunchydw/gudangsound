<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penyewaan;
use App\Models\Administrator;
use App\Services\TelegramService;

class ReminderTelegramCommand extends Command
{
    protected $signature = 'telegram:reminder';

    protected $description = 'Mengirim reminder Telegram H-2 penyewaan dan penugasan';

    public function handle()
    {
        $telegram = new TelegramService();

        // H-2
        $tanggal = now()->addDays(2)->toDateString();

        $penyewaans = Penyewaan::with([
            'penugasan.pegawai'
        ])
            ->whereDate('tanggal_mulai', $tanggal)
            ->where('status_penyewaan', '<>', 'dibatalkan')
            ->get();

        foreach ($penyewaans as $penyewaan) {

            $pesanPenyewaan =
                "⏰ PENGINGAT PENYEWAAN\n\n" .

                "Terdapat penyewaan yang akan dimulai 2 hari lagi.\n\n" .

                " Penyewa : {$penyewaan->nama_penyewa}\n" .
                " Lokasi : {$penyewaan->lokasi}\n" .
                " Tanggal : " .
                date('d-m-Y', strtotime($penyewaan->tanggal_mulai)) .
                " s/d " .
                date('d-m-Y', strtotime($penyewaan->tanggal_selesai)) .

                "\n\nSilakan membuka Sistem Administrasi HSB Audio untuk melihat detail terbaru.";

            $admins = Administrator::whereHas('roles', function ($q) {
                $q->where('slug', 'admin');
            })->get();

            foreach ($admins as $admin) {

                if (!empty($admin->telegram_chat_id)) {

                    $telegram->sendMessage(
                        $admin->telegram_chat_id,
                        $pesanPenyewaan
                    );
                }
            }

            $pemiliks = Administrator::whereHas('roles', function ($q) {
                $q->where('slug', 'pemilik');
            })->get();

            foreach ($pemiliks as $pemilik) {

                if (!empty($pemilik->telegram_chat_id)) {

                    $telegram->sendMessage(
                        $pemilik->telegram_chat_id,
                        $pesanPenyewaan
                    );
                }
            }

        // reminder penugasan H-2
            $penugasan = $penyewaan->penugasan;

            if ($penugasan) {

                $pesanPenugasan =
                    "⏰ PENGINGAT PENUGASAN\n\n" .

                    "Anda memiliki penugasan yang akan dilaksanakan 2 hari lagi.\n\n" .

                    " Penyewa : {$penyewaan->nama_penyewa}\n" .
                    " Tim : {$penugasan->tim}\n" .
                    " Lokasi : {$penyewaan->lokasi}\n" .
                    " Tanggal : " .
                    date('d-m-Y', strtotime($penyewaan->tanggal_mulai)) .
                    " s/d " .
                    date('d-m-Y', strtotime($penyewaan->tanggal_selesai)) .

                    "\n\nSilakan membuka Sistem Administrasi HSB Audio untuk melihat detail dan informasi penugasan terbaru.";

                foreach ($penugasan->pegawai as $pegawai) {

                    if (!empty($pegawai->telegram_chat_id)) {

                        $telegram->sendMessage(
                            $pegawai->telegram_chat_id,
                            $pesanPenugasan
                        );
                    }
                }
            }
        }

        $this->info('Reminder Telegram berhasil dijalankan.');

        return Command::SUCCESS;
    }
}
