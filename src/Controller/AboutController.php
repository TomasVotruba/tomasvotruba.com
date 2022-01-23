<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Website\ValueObject\RouteName;

final class AboutController extends AbstractController
{
    #[Route(path: 'about-me', name: RouteName::ABOUT)]
    public function __invoke(): Response
    {
        return $this->render('about.twig', [
            'title' => "Hi, I'm Tomas",
        ]);
    }
}
