<?php

declare(strict_types=1);

namespace Telegram;

use Telegram\Entities\Message;

class MessageFactory
{
    public function make(array $message): ?Message
    {
        try {
            return new Message($message);
        } catch (\Throwable) {
            return null;
        }
    }
}