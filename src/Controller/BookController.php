<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Website\Repository\BookRepository;
use TomasVotruba\Website\ValueObject\RouteName;

final class BookController extends AbstractController
{
    public function __construct(
        private BookRepository $bookRepository
    ) {
    }

    #[Route(path: 'books', name: RouteName::BOOKS)]
    public function __invoke(): Response
    {
        return $this->render('book/books.twig', [
            'title' => 'Books',
            'books' => $this->bookRepository->fetchAll(),
        ]);
    }
}
