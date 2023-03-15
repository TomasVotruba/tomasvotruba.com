<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\BookRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class BookDetailController extends Controller
{
    public function __construct(
        private readonly BookRepository $bookRepository
    ) {
    }

    public function __invoke(string $slug): View
    {
        $book = $this->bookRepository->getBySlug($slug);

        return \view('book/book_detail', [
            'title' => $book->getTitle(),
            'book' => $book,
        ]);
    }
}
