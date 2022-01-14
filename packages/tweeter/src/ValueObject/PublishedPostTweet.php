<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObject;

final class PublishedPostTweet
{
    public function __construct(
        private int $id,
        private \DateTimeInterface $publishedAt,
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
     * @return array{id: int, published_at: \DateTimeInterface}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'published_at' => $this->publishedAt,
        ];
    }
}
