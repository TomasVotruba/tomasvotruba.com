<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MissionController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private array $helpedCompanies = [];

    public function __construct(array $helpedCompanies)
    {
        $this->helpedCompanies = $helpedCompanies;
    }

    /**
     * @Route(path="mission", name="mission")
     */
    public function __invoke(): Response
    {
        return $this->render('mission.twig', [
            'helped_companies' => $this->helpedCompanies,
            'title' => 'The Mission',
        ]);
    }
}
