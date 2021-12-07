<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObject;

use DateTimeInterface;

final class PublishedTweet
{
    private readonly string $text;

    public function __construct(
        string $text,
        private readonly DateTimeInterface $createdAt,
        private readonly int $id,
        private readonly string $link
    ) {
        $this->text = htmlspecialchars_decode($text);
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

    public function getLink(): string
    {
        return $this->link;
    }
}
