<?php

namespace App\Jobs;

use App\Models\Penyewaan;
use App\Models\Administrator;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPenyewaanTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(
        public int $penyewaanId,
        public bool $isCreating = true
    ) {}

    public function handle(): void
    {
        $penyewaan = Penyewaan::with([
            'detailPaket.paket',
            'detailBarang.barang',
            'penugasan.pegawai',
        ])->find($this->penyewaanId);

        if (! $penyewaan) {
            return;
        }

        $penyewaan->update(['notification_status' => 'processing']);

        try {
            $judulTelegram = $this->isCreating ? "📅 PENYEWAAN BARU" : "⚠️ RALAT PENYEWAAN";

            $paket = '';
            foreach ($penyewaan->detailPaket as $detail) {
                if ($detail->paket) {
                    $paket .= "• {$detail->paket->nama_paket} x{$detail->jumlah_paket}\n";
                }
            }

            $barang = '';
            foreach ($penyewaan->detailBarang as $detail) {
                if ($detail->barang) {
                    $barang .= "• {$detail->barang->nama_barang} x{$detail->jumlah_barang}\n";
                }
            }

            $pesan = $judulTelegram . "\n\n" .
                "Penyewa : {$penyewaan->nama_penyewa}\n" .
                "No. HP : {$penyewaan->no_tlp}\n" .
                "Lokasi : {$penyewaan->lokasi}\n" .
                "Tanggal : " . date('d-m-Y', strtotime($penyewaan->tanggal_mulai)) .
                " s/d " . date('d-m-Y', strtotime($penyewaan->tanggal_selesai)) . "\n\n";

            if ($paket != '') {
                $pesan .= "📦 Paket\n" . $paket . "\n";
            }

            if ($barang != '') {
                $pesan .= "📦 Barang Satuan\n" . $barang . "\n";
            }

            $pesan .= "📝 Keterangan\n" . ($penyewaan->keterangan ?: "-") . "\n\n" .
                "💳 Status Pembayaran : " . $penyewaan->status_pembayaran;

            $telegram = new TelegramService();

            $admins = Administrator::whereHas('roles', function ($q) {
                $q->whereIn('slug', ['admin', 'pemilik']);
            })->get();

            foreach ($admins as $admin) {
                if (!empty($admin->telegram_chat_id)) {
                    $telegram->sendMessage($admin->telegram_chat_id, $pesan);
                }
            }

            if (! $this->isCreating && $penyewaan->penugasan) {
                $penugasan = $penyewaan->penugasan;
                $namaPegawai = $penugasan->pegawai->pluck('name')->implode(', ');

                $pesanRalat = "⚠️ RALAT PENUGASAN\n\n" .
                    "Penyewa : {$penyewaan->nama_penyewa}\n" .
                    "Tim : {$penugasan->tim}\n" .
                    "Lokasi : {$penyewaan->lokasi}\n" .
                    "Tanggal : " . date('d-m-Y', strtotime($penyewaan->tanggal_mulai)) .
                    " s/d " . date('d-m-Y', strtotime($penyewaan->tanggal_selesai)) . "\n\n";

                if ($paket != '') {
                    $pesanRalat .= "📦 Paket\n" . $paket . "\n";
                }

                if ($barang != '') {
                    $pesanRalat .= "📦 Barang Satuan\n" . $barang . "\n";
                }

                $pesanRalat .= "📝 Keterangan\n" . ($penyewaan->keterangan ?: '-') . "\n\n" .
                    "👷 Pegawai Bertugas\n" . $namaPegawai;

                foreach ($penugasan->pegawai as $pegawai) {
                    if (!empty($pegawai->telegram_chat_id)) {
                        $telegram->sendMessage($pegawai->telegram_chat_id, $pesanRalat);
                    }
                }
            }

            $penyewaan->update(['notification_status' => 'success']);
        } catch (\Throwable $e) {
            Log::error('Telegram Sync Failed (ID ' . $this->penyewaanId . '): ' . $e->getMessage());

            $penyewaan->update(['notification_status' => 'failed']);

            throw $e;
        }
    }
}
