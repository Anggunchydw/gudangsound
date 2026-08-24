<?php

namespace App\Jobs;

use App\Models\Penyewaan;
use App\Models\Administrator;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPenyewaanToGoogleCalendar implements ShouldQueue
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

        $penyewaan->update(['calendar_sync_status' => 'processing']);

        try {
            $emails = Administrator::whereHas('roles', function ($q) {
                $q->whereIn('slug', ['admin', 'pemilik']);
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            $calendar = new GoogleCalendarService();

            $judulGoogle = $this->isCreating
                ? 'Penyewaan Baru - ' . $penyewaan->nama_penyewa
                : 'Ralat Penyewaan - ' . $penyewaan->nama_penyewa;

            // 1. Event Utama Penyewaan
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

            // 2. Event Penugasan Pegawai (Jika ada)
            if ($penyewaan->penugasan) {
                $penugasan = $penyewaan->penugasan;

                $pegawaiEmails = $penugasan->pegawai
                    ->pluck('email')
                    ->filter()
                    ->toArray();

                $namaPegawai = $penugasan->pegawai
                    ->pluck('name')
                    ->implode(', ');

                $deskripsiPenugasan = "Penyewa : {$penyewaan->nama_penyewa}\n" .
                    "Tim : {$penugasan->tim}\n" .
                    "Lokasi : {$penyewaan->lokasi}\n" .
                    "Tanggal : " . date('d F Y', strtotime($penyewaan->tanggal_mulai)) .
                    " s/d " . date('d F Y', strtotime($penyewaan->tanggal_selesai)) .
                    "\nPegawai : {$namaPegawai}";

                if ($penugasan->google_event_id) {
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

            $penyewaan->update(['calendar_sync_status' => 'success']);
        } catch (\Throwable $e) {
            Log::error('Google Calendar Sync Failed (ID ' . $this->penyewaanId . '): ' . $e->getMessage());

            $penyewaan->update(['calendar_sync_status' => 'failed']);

            throw $e;
        }
    }
}

