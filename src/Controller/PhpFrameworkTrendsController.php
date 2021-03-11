<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\RouteName;

final class PhpFrameworkTrendsController
{
    public function __construct(
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: 'php-framework-trends', name: RouteName::PHP_FRAMEWORK_TRENDS)]
    public function __invoke(): Response
    {
        return $this->responseRenderer->render('framework-stats/php-framework-trends.twig', [
            'title' => 'PHP Framework Trends',
        ]);
    }
}
