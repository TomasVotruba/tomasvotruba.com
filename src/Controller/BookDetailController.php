<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;
use TomasVotruba\Website\Repository\BookRepository;

final class BookDetailController extends Controller
{
    public function __construct(
        private readonly BookRepository $bookRepository
    ) {
    }

    public function __invoke(string $slug): \Illuminate\Contracts\View\View
    {
        $book = $this->bookRepository->getBySlug($slug);

        return \view('book/book_detail', [
            'title' => $book->getTitle(),
            'book' => $book,
        ]);
    }
}
