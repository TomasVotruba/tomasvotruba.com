<?php

declare(strict_types=1);

namespace App\Entity;

use Illuminate\Support\Str;

/**
 * @see \App\Tests\Entity\BookTest
 */
final readonly class Book
{
    public function __construct(
        private string $title,
        private string $longDescription,
        private string $coverImage,
        private string $leanpubLink,
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

    public function getSlug(): string
    {
        return Str::slug($this->title);
    }

    public function getLongDescription(): string
    {
        return $this->longDescription;
    }
}
