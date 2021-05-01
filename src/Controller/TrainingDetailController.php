<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\RouteName;

final class TrainingDetailController
{
    public function __construct(
        private ResponseRenderer $responseRenderer
    )
    {
    }

    #[Route(path: 'training/{name}', name: RouteName::TRAINING_DETAIL)]
    public function __invoke(string $name): Response
    {
        dump($name);
        die;

        return $this->responseRenderer->render('trainings/training_detail.twig', [
            'title' => 'Trainings',
        ]);
    }
}
