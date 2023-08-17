<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

final class BookTest extends TestCase
{
    public function test(): void
    {
        $book = new Book('Some title', 'Some long description', 'Some cover image', 'Some leanpub link');

        $this->assertSame('some-title', $book->getSlug());
    }
}
