<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Website\ValueObject\RouteName;

final class TrainingsController extends AbstractController
{
    #[Route(path: 'trainings', name: RouteName::TRAININGS)]
    public function __invoke(): Response
    {
        return $this->render('trainings/trainings.twig', [
            'title' => 'Trainings',
        ]);
    }
}
