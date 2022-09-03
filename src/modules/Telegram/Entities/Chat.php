<?php

declare(strict_types=1);

namespace Telegram\Entities;

class Chat
{
    private int $id;
    private ?string $username;
    private ?string $firstName;
    private string $type;

    public function __construct(array $properties)
    {
        $this->id = $properties['id'];
        $this->username = $properties['username'] ?? null;
        $this->firstName = $properties['first_name'] ?? null;
        $this->type = $properties['type'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getType(): string
    {
        return $this->type;
    }
}