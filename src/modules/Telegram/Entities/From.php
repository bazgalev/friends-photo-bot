<?php

declare(strict_types=1);

namespace Telegram\Entities;

class From
{
    private int $id;
    private bool $isBot;
    private string $firstName;
    private ?string $userName;
    private ?string $languageCode;

    public function __construct(array $properties)
    {
        $this->id = $properties['id'];
        $this->isBot = (bool)$properties['is_bot'];
        $this->firstName = $properties['first_name'];
        $this->userName = $properties['username'] ?? null;
        $this->languageCode = $properties['language_code'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isBot(): bool
    {
        return $this->isBot;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }
}