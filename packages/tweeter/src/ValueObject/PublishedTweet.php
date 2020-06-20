<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObject;

use DateTimeInterface;

final class PublishedTweet
{
    private string $text;

    private int $id;

    private DateTimeInterface $createdAt;

    public function __construct(string $text, DateTimeInterface $createdAt, int $id)
    {
        $this->text = htmlspecialchars_decode($text);
        $this->id = $id;
        $this->createdAt = $createdAt;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
