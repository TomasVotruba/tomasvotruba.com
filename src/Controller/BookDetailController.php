<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use TomasVotruba\Website\Repository\BookRepository;
use TomasVotruba\Website\ValueObject\RouteName;

final class BookDetailController extends AbstractController
{
    public function __construct(
        private readonly BookRepository $bookRepository
    ) {
    }

    #[Route(path: 'book/{slug}', name: RouteName::BOOK_DETAIL)]
    public function __invoke(string $slug): Response
    {
        $book = $this->bookRepository->getBySlug($slug);

        return $this->render('book/book_detail.twig', [
            'title' => $book->getTitle(),
            'book' => $book,
        ]);
    }
}
