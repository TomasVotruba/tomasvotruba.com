<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObject;

use DateTimeInterface;

final class PostTweet
{
    private string $text;

    public function __construct(
        private int $postId,
        string $text,
        private DateTimeInterface $dateTime,
        private ?string $image,
        private string $link
    ) {
        $this->text = htmlspecialchars_decode($text);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPostDateTimeInFormat(string $format): string
    {
        return $this->dateTime->format($format);
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getId(): int
    {
        return $this->postId;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
