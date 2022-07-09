<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

final class DjMix
{
    public function __construct(
        private readonly string $title,
        private readonly string $url,
        private readonly string $imageUrl,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
}
