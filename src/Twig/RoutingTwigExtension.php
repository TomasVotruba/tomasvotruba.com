<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoutingTwigExtension extends AbstractExtension
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        $isCurrentRouteTwigFunction = new TwigFunction(
            'is_current_route',
            fn (string $desiredRouteName): bool => $this->isCurrentRoute($desiredRouteName)
        );

        $isCurrentRoutesTwigFunction = new TwigFunction('is_current_routes', function (array $desiredRouteNames): bool {
            foreach ($desiredRouteNames as $desiredRouteName) {
                if (! $this->isCurrentRoute($desiredRouteName)) {
                    continue;
                }

                return true;
            }

            return false;
        });

        return [$isCurrentRouteTwigFunction, $isCurrentRoutesTwigFunction];
    }

    private function isCurrentRoute(string $desiredRouteName): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if ($currentRequest === null) {
            throw new ShouldNotHappenException();
        }

        return $this->resolveCurrentRoute($currentRequest) === $desiredRouteName;
    }

    private function resolveCurrentRoute(Request $request): string
    {
        $currentRouteName = $request->get('_route');

        return ltrim($currentRouteName, '/');
    }
}
