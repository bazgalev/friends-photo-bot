<?php

namespace Vk;

use GuzzleHttp\Client as Guzzle;

class Client
{
    private Guzzle $guzzle;
    private Config $config;

    public function __construct(Guzzle $guzzle, Config $config)
    {
        $this->guzzle = $guzzle;
        $this->config = $config;
    }

    public function photosCount(): int
    {
        $body = [
            'owner_id' => $this->config->getOwnerId(),
            'extended' => false,
            'offset' => 0,
            'photo_sizes' => false,
            'count' => 0,
        ];

        $response = $this->post('photos.getAll', $body);

        return $response['response']['count'];
    }

    public function getRandomPhotoUrl(): string
    {
        $offset = random_int(0, $this->photosCount() - 1);

        $body = [
            'owner_id' => $this->config->getOwnerId(),
            'extended' => false,
            'offset' => $offset,
            'photo_sizes' => true,
            'count' => 1,
        ];

        $response = $this->post('photos.getAll', $body);

        $sizes = $response['response']['items'][0]['sizes'];
        $max['width'] = 0;

        foreach ($sizes as $size) {
            if ($size['width'] > $max['width']) {
                $max = $size;
            }
        }

        return $max['url'];
    }

    public function getRandomQuote(): array
    {
        $body = [
            'group_id' => abs($this->config->getOwnerId()),
            'topic_id' => $this->config->getQuoteTopicId(),
            'count' => 100,
        ];

        $response = $this->post('board.getComments', $body)['response'];
        $index = random_int(1, $response['count'] - 1);

        return $response['items'][$index];
    }

    private function post(string $uri, array $body): array
    {
        $body['access_token'] = $this->config->getAccessToken();
        $body['v'] = '5.131';

        $response = $this->guzzle->post($uri, ['form_params' => $body]);
        $response = $response->getBody()->getContents();

        return json_decode($response, true);
    }
}