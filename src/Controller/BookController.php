<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use TomasVotruba\Website\ValueObject\RouteName;

final class BookController extends AbstractController
{
    #[Route(path: 'book/the-power-of-automated-refactoring', name: RouteName::BOOK)]
    public function __invoke(): Response
    {
        return $this->render('book/book.twig', [
            'title' => 'Rector - The Power of Automated&nbsp;Refactoring ',
        ]);
    }
}
