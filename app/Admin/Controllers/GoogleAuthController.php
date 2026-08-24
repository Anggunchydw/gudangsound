<?php

namespace App\Admin\Controllers;

use App\Services\GoogleCalendarService;
use Dcat\Admin\Http\Controllers\AdminController;
use Google\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GoogleAuthController extends AdminController
{
    public function login()
    {
        $client = new Client();
        $client->setApplicationName('HSB Rental');
        $client->setScopes([
            \Google\Service\Calendar::CALENDAR,
        ]);

        $client->setAuthConfig(
            storage_path('app/google/client_secret.json')
        );

        $client->setRedirectUri(
            env('GOOGLE_REDIRECT_URI', url('/admin/google/callback'))
        );

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        // 1. Buat token state acak & simpan di session untuk mencegah OAuth CSRF
        $state = Str::random(40);
        session(['google_oauth_state' => $state]);

        $client->setState($state);

        $url = $client->createAuthUrl();

        return redirect($url);
    }

    public function callback(Request $request)
    {
        // 2. Validasi parameter state menggunakan perbandingan timing-safe
        $savedState = session('google_oauth_state');

        if (
            empty($request->state) ||
            empty($savedState) ||
            !hash_equals($savedState, $request->state)
        ) {
            abort(403, 'Sesi OAuth tidak valid atau telah kedaluwarsa (Invalid State Parameter).');
        }

        // Hapus token state dari session setelah tervalidasi
        session()->forget('google_oauth_state');

        if (!$request->has('code')) {
            abort(400, 'Authorization code tidak ditemukan.');
        }

        $client = new Client();
        $client->setApplicationName('HSB Rental');
        $client->setScopes([
            \Google\Service\Calendar::CALENDAR,
        ]);

        $client->setAuthConfig(
            storage_path('app/google/client_secret.json')
        );

        $client->setRedirectUri(
            env('GOOGLE_REDIRECT_URI', url('/admin/google/callback'))
        );

        $client->setAccessType('offline');

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return "Gagal mendapatkan token Google: " . ($token['error_description'] ?? $token['error']);
        }

        // Pastikan direktori tujuan ada sebelum menyimpan token
        if (!is_dir(storage_path('app/google'))) {
            mkdir(storage_path('app/google'), 0755, true);
        }

        file_put_contents(
            storage_path('app/google/token.json'),
            json_encode($token, JSON_PRETTY_PRINT)
        );

        return "Google Calendar berhasil terhubung.";
    }

    // public function testCalendar()
    // {
    //     $google = new GoogleCalendarService();

    //     $event = $google->createEvent(
    //         'TEST HSB',
    //         'HSB Audio',
    //         'Percobaan Event',
    //         now()->addMinute()->format('Y-m-d\TH:i:s'),
    //         now()->addHour()->format('Y-m-d\TH:i:s')
    //     );

    //     return $event->htmlLink;
    // }
}
