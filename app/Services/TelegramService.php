<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class TelegramService
{
    protected $token;

    public function __construct()
    {
       
        $this->token = config('services.telegram.bot_token');
    }

    public function sendMessage($chatId, $message)
    {
        if (empty($this->token)) {
            throw new Exception('TELEGRAM_BOT_TOKEN belum diatur pada konfigurasi aplikasi.');
        }

        if (empty($chatId)) {
            return false;
        }

        $response = Http::timeout(10)->post(
            "https://api.telegram.org/bot{$this->token}/sendMessage",
            [
                'chat_id' => $chatId,
                'text'    => $message,
            ]
        )->throw();

        $data = $response->json();
        if (isset($data['ok']) && $data['ok'] === false) {
            throw new Exception('Telegram API Error: ' . ($data['description'] ?? 'Gagal mengirim pesan.'));
        }

        return $response;
    }
}
