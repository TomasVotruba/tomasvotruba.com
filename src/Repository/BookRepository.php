<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Repository;

use TomasVotruba\Website\Exception\BookException;
use TomasVotruba\Website\ValueObject\Book;

final class BookRepository
{
    /**
     * @var Book[]
     */
    private array $books;

    public function __construct()
    {
        $this->books = [
            new Book(
                'Upgrade Every Day',
                'Contrary to common experience, upgrading is easy. Learn completely different approach to upgrading, based on daily little steps that accumulate to codebase that never ages.',
                'https://d2sofvawe08yqg.cloudfront.net/upgrade-every-day/s_hero2x?1660495665',
                'https://leanpub.com/upgrade-every-day',
                false
            ),

            new Book(
                'Rector - The Power of Automated Refactoring',
                'Learn to master Rector, to improve your everyday coding, and solving huge changes without effort',
                'https://d2sofvawe08yqg.cloudfront.net/rector-the-power-of-automated-refactoring/s_hero2x?1651754329',
                'https://leanpub.com/rector-the-power-of-automated-refactoring',
                true
            ),
        ];
    }

    /**
     * @return Book[]
     */
    public function fetchAll(): array
    {
        return $this->books;
    }

    public function getBySlug(string $slug): Book
    {
        foreach ($this->books as $book) {
            if ($book->getSlug() === $slug) {
                return $book;
            }
        }

        $errorMessage = sprintf('Book slug "%s" was not found', $slug);
        throw new BookException($errorMessage);
    }
}
