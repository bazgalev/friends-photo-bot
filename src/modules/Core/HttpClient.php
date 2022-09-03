<?php

namespace Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;

class HttpClient extends Client
{
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        try {
            return parent::request($method, $uri, $options);
        } catch (ClientException | ConnectException) {
            sleep(1);
            return parent::request($method, $uri, $options);
        }
    }
}