<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class GoogleCalendarService
{
    protected Client $client;
    protected Calendar $service;

    public function __construct()
    {
        $this->client = new Client();

        $this->client->setApplicationName('HSB Rental');

        $this->client->setScopes([
            Calendar::CALENDAR,
        ]);

        $this->client->setAuthConfig(
            storage_path('app/google/client_secret.json')
        );

        $this->client->setAccessType('offline');

        $tokenPath = storage_path('app/google/token.json');

        if (!file_exists($tokenPath)) {
            throw new \Exception('Token Google belum tersedia.');
        }

        $token = json_decode(
            file_get_contents($tokenPath),
            true
        );

        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {

            $this->client->fetchAccessTokenWithRefreshToken(
                $token['refresh_token']
            );

            file_put_contents(
                $tokenPath,
                json_encode($this->client->getAccessToken())
            );
        }

        $this->service = new Calendar($this->client);
    }

    public function createEvent(

        $judul,
        $lokasi,
        $deskripsi,
        $tanggalMulai,
        $tanggalSelesai,
        array $emails = []
    ) {

        $attendees = [];

        foreach ($emails as $email) {

            $attendees[] = [
                'email' => $email,
            ];
        }

        $event = new \Google\Service\Calendar\Event([

            'summary' => $judul,

            'location' => $lokasi,

            'description' => $deskripsi,
            'start' => [
                'dateTime' => $tanggalMulai,
                'timeZone' => 'Asia/Jakarta',
            ],

            'end' => [
                'dateTime' => $tanggalSelesai,
                'timeZone' => 'Asia/Jakarta',
            ],
            // 'start' => [
            //     'date' => $tanggalMulai,
            // ],

            // 'end' => [
            //     'date' => \Carbon\Carbon::parse($tanggalSelesai)
            //         ->addDay()
            //         ->toDateString(),
            // ],

            'attendees' => $attendees,

            'reminders' => [

                'useDefault' => false,

                'overrides' => [

                    // Email 2 hari sebelum acara
                    [
                        'method' => 'email',
                        'minutes' => 5, //2880
                    ],

                    // Notifikasi Google 2 hari
                    [
                        'method' => 'popup',
                        'minutes' => 5,
                    ],

                ],

            ],

        ]);

        return $this->service
            ->events
            ->insert(
                'primary',
                $event,
                [
                    'sendUpdates' => 'all'
                ]
            );
    }

    public function service()
    {
        return $this->service;
    }
}
