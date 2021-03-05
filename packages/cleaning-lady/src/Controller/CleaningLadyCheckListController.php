<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\CleaningLady\CleaningCheckList\CleaningCheckListFactory;
use TomasVotruba\Website\ValueObject\RouteName;

final class CleaningLadyCheckListController
{
    public function __construct(
        private CleaningCheckListFactory $cleaningCheckListFactory,
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: 'cleaning-lady-checklist', name: RouteName::CLEANING_LADY_CHECKLIST)]
    public function __invoke(): Response
    {
        return $this->responseRenderer->render('cleaning_lady_checklist.twig', [
            'title' => 'Cleaning Lady Check list',
            'checklist' => $this->cleaningCheckListFactory->create(),
        ]);
    }
}
