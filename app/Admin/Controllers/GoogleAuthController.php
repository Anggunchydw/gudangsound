<?php

namespace App\Admin\Controllers;

use App\Services\GoogleCalendarService;
use Dcat\Admin\Http\Controllers\AdminController;
use Google\Client;
use Illuminate\Http\Request;

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
            'http://127.0.0.1:8000/admin/google/callback'
        );

        $client->setAccessType('offline');

        $client->setPrompt('consent');

        $url = $client->createAuthUrl();


        return redirect($url);
    }


    public function callback(Request $request)
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
            'http://127.0.0.1:8000/admin/google/callback'
        );
        $client->setAccessType('offline');


        $token = $client->fetchAccessTokenWithAuthCode(
            $request->code
        );


        if (isset($token['error'])) {

            return "Gagal mendapatkan token Google";
        }


        file_put_contents(
            storage_path('app/google/token.json'),
            json_encode($token)
        );


        return "Google Calendar berhasil terhubung";
    }
    public function testCalendar()
{
    $google = new GoogleCalendarService();

    $event = $google->createEvent(

        'TEST HSB',

        'HSB Audio',

        'Percobaan Event',

        now()->addMinute()->format('Y-m-d\TH:i:s'),

        now()->addHour()->format('Y-m-d\TH:i:s')
    );

    return $event->htmlLink;
}
}
