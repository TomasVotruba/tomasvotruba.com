<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\Option;
use TomasVotruba\Website\ValueObject\RouteName;

final class BooksController
{
    public function __construct(
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: 'books', name: RouteName::BOOKS)]
    public function __invoke(): Response
    {
        return $this->responseRenderer->render('book/books.twig', [
            'title' => 'The Mission',
        ]);
    }
}
