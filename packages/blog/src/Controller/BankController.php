<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Templating\ResponseRenderer;

final class BankController
{
    public function __construct(
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: '/bank')]
    public function __invoke(): Response
    {
        return $this->responseRenderer->render('blog/bank.twig', [
            'title' => 'Bank Details',
        ]);
    }
}
