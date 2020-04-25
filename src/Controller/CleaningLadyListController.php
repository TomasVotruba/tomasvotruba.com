<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CleaningLadyListController extends AbstractController
{
    /**
     * @Route(path="cleaning-lady-list", name="cleaning_lady")
     */
    public function __invoke(): Response
    {
        // @todo value object
        $checklist = [];
        $checklist[] = [
            'headline' => 'composer.json',
            'items' => [
                'make sure php version is specified',
            ]
        ];

        return $this->render('cleaning_lady_list.twig', [
            'title' => 'Cleaning Lady list',
            'checklist' => $checklist
        ]);
    }
}
