<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\Option;
use TomasVotruba\Website\ValueObject\RouteName;

final class BookDetailController
{
    public function __construct(
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: 'book/{slug}', name: RouteName::BOOK_DETAIL)]
    public function __invoke(string $slug): Response
    {
        return $this->responseRenderer->render('book/book_detail.twig', [
            'title' => 'The Mission',
        ]);
    }
}
