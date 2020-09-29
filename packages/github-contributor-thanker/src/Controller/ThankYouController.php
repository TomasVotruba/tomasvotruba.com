<?php

declare(strict_types=1);

namespace TomasVotruba\GithubContributorsThanker\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\GithubContributorsThanker\ValueObject\Option;

final class ThankYouController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private array $contributors = [];

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->contributors = $parameterProvider->provideArrayParameter(Option::CONTRIBUTORS);
    }

    /**
     * @Route(path="thank-you", name="thank_you")
     */
    public function __invoke(): Response
    {
        $mostActiveContributors = array_slice($this->contributors, 0, 9);
        $otherContributors = array_slice($this->contributors, 9);

        return $this->render('thank_you.twig', [
            'title' => 'Thank You',
            'most_active_contributors' => $mostActiveContributors,
            'other_contributors' => $otherContributors,
        ]);
    }
}
