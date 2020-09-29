<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class TalksController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private array $talks = [];

    /**
     * @var mixed[]
     */
    private array $talksFeedback = [];

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->talks = $parameterProvider->provideArrayParameter('talks');
        $this->talksFeedback = $parameterProvider->provideArrayParameter('talks_feedback');
    }

    /**
     * @Route(path="talks", name="talks")
     */
    public function __invoke(): Response
    {
        return $this->render('talks.twig', [
            'title' => 'Talks',
            'talks' => $this->talks,
            'talks_feedback' => $this->talksFeedback,
        ]);
    }
}
