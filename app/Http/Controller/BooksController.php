<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use TomasVotruba\Website\Repository\BookRepository;

final class BooksController extends Controller
{
    public function __construct(
        private readonly BookRepository $bookRepository
    ) {
    }

    public function __invoke(): View
    {
        return \view('book/books', [
            'title' => 'Books',
            'books' => $this->bookRepository->fetchAll(),
        ]);
    }
}
