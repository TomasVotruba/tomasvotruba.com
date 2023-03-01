<?php

declare(strict_types=1);

namespace App\Entity;

use Illuminate\Support\Str;

/**
 * @api
 * @see \App\Tests\Entity\BookTest
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
        return Str::slug($this->title);
    }

    public function getLongDescription(): string
    {
        return $this->longDescription;
    }
}
