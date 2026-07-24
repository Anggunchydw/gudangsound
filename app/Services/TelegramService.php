<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $token;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
    }

    public function sendMessage($chatId, $message)
    {
        return Http::post(
            "https://api.telegram.org/bot{$this->token}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $message,
            ]
        );
    }
}
