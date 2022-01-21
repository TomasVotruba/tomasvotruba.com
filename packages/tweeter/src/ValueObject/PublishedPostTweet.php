<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObject;

use DateTimeInterface;

final class PublishedPostTweet
{
    public function __construct(
        private readonly int $id,
        private readonly DateTimeInterface $publishedAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPublishedAt(): \DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * @return array{id: int, published_at: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'published_at' => $this->publishedAt->format('Y-m-d'),
        ];
    }
}
