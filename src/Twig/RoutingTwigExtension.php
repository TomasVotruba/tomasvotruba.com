<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoutingTwigExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): iterable
    {
        yield new TwigFunction('is_current_route', function (string $desiredRouteName): bool {
            $currentRequest = $this->requestStack->getCurrentRequest();
            if ($currentRequest === null) {
                throw new ShouldNotHappenException();
            }

            $currentRouteName = $currentRequest->get('_route');
            $currentRouteName = ltrim($currentRouteName, '/');

            return $currentRouteName === $desiredRouteName;
        });
    }
}
