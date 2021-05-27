<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Website\ValueObject\RouteName;

final class ContactController extends AbstractController
{
    #[Route(path: 'contact', name: RouteName::CONTACT)]
    public function __invoke(): Response
    {
        return $this->render('contact.twig', [
            'title' => 'Get in Touch',
        ]);
    }
}
