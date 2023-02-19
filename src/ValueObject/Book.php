<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @api
 * @see \TomasVotruba\Website\Tests\ValueObject\BookTest
 */
final class Book
{
    public function __construct(
        private readonly string $title,
        private readonly string $longDescription,
        private readonly string $coverImage,
        private readonly string $leanpubLink,
        private readonly bool $isFinished,
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

    public function getSlug(): string
    {
        $asciiSlugger = new AsciiSlugger();

        return $asciiSlugger->slug($this->title)
            ->lower()
            ->toString();
    }

    public function getLongDescription(): string
    {
        return $this->longDescription;
    }
}
