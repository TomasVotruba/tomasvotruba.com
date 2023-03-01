<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Entity\Book;

final class BookTest extends TestCase
{
    public function test(): void
    {
        $book = new Book('Some title', 'Some long description', 'Some cover image', 'Some leanpub link', true);

        $this->assertSame('some-title', $book->getSlug());
    }
}
