<?php

namespace Telegram;

class Whitelist
{
    private array $whitelist;

    public function __construct(array $whitelist)
    {
        $this->whitelist = $whitelist;
    }

    public function has(int $chat): bool
    {
        return in_array($chat, $this->whitelist);
    }
}