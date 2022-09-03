<?php

namespace Telegram;

use GuzzleHttp\Client as Guzzle;

class Client
{
    private Guzzle $guzzle;

    public function __construct(Guzzle $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function publishPhoto(string $url, int $chatId, string $caption = null): void
    {
        $body = [
            'photo' => $url,
            'chat_id' => $chatId,
        ];

        if ($caption) {
            $body['caption'] = substr($caption, 0, 1000);
        }

        $this->guzzle->post('sendPhoto', ['form_params' => $body]);
    }

    public function getUpdates(int $offset = 0): array
    {
        $response = $this->guzzle->post('getUpdates', [
            'form_params' => [
                'offset' => $offset,
                'timeout' => 10,
                'allowed_updates' => json_encode(['message']),
            ]
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        return $response['result'];

    }

    public function publishVoice(string $chatId, string $fileId): void
    {
        $this->guzzle->post('sendVoice', [
            'form_params' => [
                'chat_id' => $chatId,
                'voice' => $fileId,
            ]
        ]);
    }

    public function sendText(string $chatId, string $text): void
    {
        $this->guzzle->post('sendMessage', [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => $text,
            ],
        ]);
    }
}