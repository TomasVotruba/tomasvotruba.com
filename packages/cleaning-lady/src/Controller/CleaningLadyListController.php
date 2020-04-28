<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\CleaningLady\CleaningCheckList\CleaningCheckListFactory;

final class CleaningLadyListController extends AbstractController
{
    private CleaningCheckListFactory $cleaningCheckListFactory;

    public function __construct(CleaningCheckListFactory $cleaningCheckListFactory)
    {
        $this->cleaningCheckListFactory = $cleaningCheckListFactory;
    }

    /**
     * @Route(path="cleaning-lady-list", name="cleaning_lady")
     */
    public function __invoke(): Response
    {
        return $this->render('cleaning_lady_list.twig', [
            'title' => 'Cleaning Lady list',
            'checklist' => $this->cleaningCheckListFactory->create(),
        ]);
    }
}
