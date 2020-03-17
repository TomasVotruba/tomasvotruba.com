<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObject;

final class PublishedTweet
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = htmlspecialchars_decode($text);
    }

    public function getText(): string
    {
        return $this->text;
    }
}
