<?php

declare(strict_types=1);

namespace App\Services;

use Telegram\Client as TelegramClient;
use Telegram\CommandHandlerInterface;
use Telegram\Entities\Message;
use Vk\Client as VkClient;

class PhotoPublisher implements CommandHandlerInterface
{
    private VkClient $vkClient;
    private TelegramClient $telegramClient;

    public function __construct(VkClient $vkClient, TelegramClient $telegramClient)
    {
        $this->vkClient = $vkClient;
        $this->telegramClient = $telegramClient;
    }

    public function publishRandom(int $chatId, string $message = null): void
    {
        $url = $this->vkClient->getRandomPhotoUrl();
        $this->telegramClient->publishPhoto($url, $chatId, $message);
    }

    public function handle(Message $message): void
    {
        $this->publishRandom($message->getChat()->getId());
    }
}
