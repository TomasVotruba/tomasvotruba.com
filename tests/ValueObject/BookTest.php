<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\ValueObject\Book;

final class BookTest extends TestCase
{
    public function test(): void
    {
        $book = new Book(
            'Some title',
            'Some description',
            'Some long description',
            'Some cover image',
            'Some leanpub link',
            true
        );

        $this->assertSame('some-title', $book->getSlug());
    }
}
