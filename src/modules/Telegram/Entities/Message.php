<?php

declare(strict_types=1);

namespace Telegram\Entities;

use DateTime;

class Message
{
    protected int $messageId;
    protected ?From $from;
    protected Chat $chat;
    protected DateTime $date;
    protected ?string $text;

    public function __construct(array $message)
    {
        $this->messageId = $message['message_id'];
        $this->from = new From($message['from']) ?? null;
        $this->chat = new Chat($message['chat']);
        $this->setDateFromTimestamp($message['date']);
        $this->text = $message['text'] ?? null;
    }

    private function setDateFromTimestamp(int $timestamp): void
    {
        $dt = new DateTime();
        $dt->setTimestamp($timestamp);

        $this->date = $dt;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getFrom(): ?From
    {
        return $this->from;
    }

    public function getChat(): Chat
    {
        return $this->chat;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function hasText(): bool
    {
        return !is_null($this->getText());
    }

    /**
     * Не совсем то, что нужно, но пока так. Нужно подумать и порефакторить.
     */
    public function extractCommand(): ?string
    {
        if (!$this->hasText()) {
            return null;
        }

        $command = stristr($this->getText(), '@', true);

        return $command ?: $this->getText();
    }
}