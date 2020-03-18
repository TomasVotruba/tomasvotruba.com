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
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): iterable
    {
        $isCurrentRoute = new TwigFunction(
            'is_current_route',
            fn (string $desiredRouteName): bool => $this->isCurrentRoute($desiredRouteName)
        );

        $isCurrentRoutes = new TwigFunction('is_current_routes', function (array $desiredRouteNames): bool {
            foreach ($desiredRouteNames as $desiredRouteName) {
                if (! $this->isCurrentRoute($desiredRouteName)) {
                    continue;
                }

                return true;
            }

            return false;
        });

        return [$isCurrentRoute, $isCurrentRoutes];
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
