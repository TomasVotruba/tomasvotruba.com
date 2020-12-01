<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Website\ValueObject\Option;

final class MissionController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private array $helpedCompanies = [];

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->helpedCompanies = $parameterProvider->provideArrayParameter(Option::HELPED_COMPANIES);
    }

    #[Route('mission', name: 'mission')]
    public function __invoke(): Response
    {
        return $this->render('mission.twig', [
            'helped_companies' => $this->helpedCompanies,
            'title' => 'The Mission',
        ]);
    }
}
