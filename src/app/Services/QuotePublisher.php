<?php

declare(strict_types=1);

namespace App\Services;

use Telegram\CommandHandlerInterface;
use Telegram\Entities\Message;
use Vk\Client as VkClient;

class QuotePublisher implements CommandHandlerInterface
{
    private PhotoPublisher $photoPublisher;
    private VkClient $vkClient;

    public function __construct(PhotoPublisher $photoPublisher, VkClient $vkClient)
    {
        $this->photoPublisher = $photoPublisher;
        $this->vkClient = $vkClient;
    }

    public function publish(int $chatId): void
    {
        $quote = $this->vkClient->getRandomQuote()['text'] . '))';
        $this->photoPublisher->publishRandom($chatId, $quote);
    }

    public function handle(Message $message): void
    {
        $this->publish($message->getChat()->getId());
    }
}