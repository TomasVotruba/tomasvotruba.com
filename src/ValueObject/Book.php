<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

use Symfony\Component\String\Slugger\AsciiSlugger;

final class Book
{
    public function __construct(
        private string $title,
        private string $description,
        private string $coverImage,
        private string $leanpubLink,
        private bool $isFinished,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCoverImage(): string
    {
        return $this->coverImage;
    }

    public function getLeanpubLink(): string
    {
        return $this->leanpubLink;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        $asciiSlugger = new AsciiSlugger();

        return $asciiSlugger->slug($this->title)
            ->lower()
            ->toString();
    }
}
