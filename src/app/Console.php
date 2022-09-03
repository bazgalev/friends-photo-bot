<?php

namespace App;

/**
 * Запускается на кроне.
 */
class Console
{
    private Di $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function execute(): void
    {
        echo __METHOD__ . " started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $this->di->quotePublisher()->publish($this->di->telegramChatId());

        echo __METHOD__ . " finished at " . date('Y-m-d H:i:s') . PHP_EOL;
    }
}