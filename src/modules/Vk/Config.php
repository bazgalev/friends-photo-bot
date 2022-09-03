<?php

declare(strict_types=1);

namespace Vk;

class Config
{
    private string $accessToken;

    private int $ownerId;

    private int $quoteTopicId;

    public function __construct(array $config)
    {
        $this->accessToken = $config['access_token'];
        $this->ownerId = $config['owner_id'];
        $this->quoteTopicId = $config['quote_topic_id'];
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getQuoteTopicId(): int
    {
        return $this->quoteTopicId;
    }
}