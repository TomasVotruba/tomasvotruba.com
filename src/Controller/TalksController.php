<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Website\ValueObject\Option;

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
        $this->talks = $parameterProvider->provideArrayParameter(Option::TALKS);
        $this->talksFeedback = $parameterProvider->provideArrayParameter(Option::TALKS_FEEDBACK);
    }

    #[Route('talks', name: 'talks')]
    public function __invoke(): Response
    {
        return $this->render('talks.twig', [
            'title' => 'Talks',
            'talks' => $this->talks,
            'talks_feedback' => $this->talksFeedback,
        ]);
    }
}
