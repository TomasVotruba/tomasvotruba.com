<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\CleaningLady\CleaningCheckList\CleaningCheckListFactory;
use TomasVotruba\Website\ValueObject\RouteName;

final class CleaningLadyCheckListController extends AbstractController
{
    public function __construct(private CleaningCheckListFactory $cleaningCheckListFactory)
    {
    }

    #[Route(path: 'cleaning-lady-checklist', name: RouteName::CLEANING_LADY_CHECKLIST)]
    public function __invoke(): Response
    {
        return $this->render('cleaning_lady_checklist.twig', [
            'title' => 'Cleaning Lady Check list',
            'checklist' => $this->cleaningCheckListFactory->create(),
        ]);
    }
}
