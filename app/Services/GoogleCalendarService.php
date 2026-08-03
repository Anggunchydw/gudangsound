<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventReminders;

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
        $this->client->setPrompt('consent');

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

            $newToken = $this->client->getAccessToken();

            if (
                empty($newToken['refresh_token']) &&
                !empty($token['refresh_token'])
            ) {
                $newToken['refresh_token'] = $token['refresh_token'];
            }

            file_put_contents(
                $tokenPath,
                json_encode($newToken)
            );
        }

        $this->service = new Calendar($this->client);
    }

    /**
     * CREATE EVENT
     */
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

        $event = new Event([

            'summary' => $judul,

            'location' => $lokasi,

            'description' => $deskripsi,

            'start' => [
                'date' => $tanggalMulai,
            ],

            'end' => [
                'date' => Carbon::parse($tanggalSelesai)
                    ->addDay()
                    ->toDateString(),
            ],

            'attendees' => $attendees,

            'reminders' => [

                'useDefault' => false,

                'overrides' => [

                    [
                        'method' => 'email',
                        'minutes' => 2880,
                    ],

                    [
                        'method' => 'popup',
                        'minutes' => 2880,
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

    /**
     * UPDATE EVENT
     */
    public function updateEvent(
        $eventId,
        $judul,
        $lokasi,
        $deskripsi,
        $tanggalMulai,
        $tanggalSelesai,
        array $emails = []
    ) {

        $event = $this->service
            ->events
            ->get('primary', $eventId);

        $event->setSummary($judul);

        $event->setLocation($lokasi);

        $event->setDescription($deskripsi);

        // START
        $start = new EventDateTime();
        $start->setDate($tanggalMulai);

        // END
        $end = new EventDateTime();
        $end->setDate(
            Carbon::parse($tanggalSelesai)
                ->addDay()
                ->toDateString()
        );

        $event->setStart($start);
        $event->setEnd($end);

        // ATTENDEES
        $attendees = [];

        foreach ($emails as $email) {

            $attendees[] = [
                'email' => $email,
            ];
        }

        $event->setAttendees($attendees);

        // REMINDER
        $reminders = new EventReminders();

        $reminders->setUseDefault(false);

        $reminders->setOverrides([
            [
                'method' => 'email',
                'minutes' => 2880,
            ],
            [
                'method' => 'popup',
                'minutes' => 2880,
            ],
        ]);

        $event->setReminders($reminders);

        return $this->service
            ->events
            ->update(
                'primary',
                $eventId,
                $event,
                [
                    'sendUpdates' => 'all'
                ]
            );
    }

    /**
     * DELETE EVENT
     */
    public function deleteEvent($eventId)
    {
        return $this->service
            ->events
            ->delete('primary', $eventId);
    }

    public function service()
    {
        return $this->service;
    }
}
