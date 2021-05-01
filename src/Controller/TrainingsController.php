<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\RouteName;

final class TrainingsController
{
    public function __construct(
        private ResponseRenderer $responseRenderer
    )
    {
    }

    #[Route(path: 'trainings', name: RouteName::TRAININGS)]
    public function __invoke(): Response
    {
        return $this->responseRenderer->render('trainings/trainings.twig', [
            'title' => 'Trainings',
        ]);
    }
}
