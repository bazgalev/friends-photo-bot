<?php

declare(strict_types=1);

namespace Telegram;

use Telegram\Entities\Message;

interface CommandHandlerInterface
{
    public function handle(Message $message): void;
}