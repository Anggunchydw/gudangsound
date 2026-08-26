<?php

namespace App\Admin\Controllers;

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

        $redirectUri = config('services.google.redirect_uri') ?: url('/admin/google/callback');
        $client->setRedirectUri($redirectUri);

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $state = Str::random(40);
        session(['google_oauth_state' => $state]);

        $client->setState($state);

        $url = $client->createAuthUrl();

        return redirect($url);
    }

    public function callback(Request $request)
    {
        $savedState = session('google_oauth_state');

        if (
            empty($request->state) ||
            empty($savedState) ||
            !hash_equals($savedState, $request->state)
        ) {
            abort(403, 'Sesi OAuth tidak valid atau telah kedaluwarsa (Invalid State Parameter).');
        }

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

        $redirectUri = config('services.google.redirect_uri') ?: url('/admin/google/callback');
        $client->setRedirectUri($redirectUri);

        $client->setAccessType('offline');

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return "Gagal mendapatkan token Google: " . ($token['error_description'] ?? $token['error']);
        }

        $googleDir = storage_path('app/google');
        $tokenPath = $googleDir . '/token.json';

        if (!is_dir($googleDir)) {
            mkdir($googleDir, 0700, true);
        }

        file_put_contents(
            $tokenPath,
            json_encode($token, JSON_PRETTY_PRINT)
        );

        chmod($tokenPath, 0600);

        return "Google Calendar berhasil terhubung.";
    }
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
