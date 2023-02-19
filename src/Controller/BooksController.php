<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;
use TomasVotruba\Website\Repository\BookRepository;

final class BooksController extends Controller
{
    public function __construct(
        private readonly BookRepository $bookRepository
    ) {
    }

    public function __invoke(): \Illuminate\Contracts\View\View
    {
        return \view('book/books', [
            'title' => 'Books',
            'books' => $this->bookRepository->fetchAll(),
        ]);
    }
}
