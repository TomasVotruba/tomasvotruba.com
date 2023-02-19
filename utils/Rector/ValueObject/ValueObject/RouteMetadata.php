<?php

declare(strict_types=1);

namespace TomasVotruba\Utils\Rector\ValueObject\ValueObject;

final class RouteMetadata
{
    /**
     * @param array<string, mixed> $routeRequirements
     */
    public function __construct(
        private readonly string $routePath,
        private readonly string $routeTarget,
        private readonly ?string $routeName,
        private readonly array $routeRequirements,
    ) {
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * @api use later
     * @return array<string, mixed>
     */
    public function getRouteRequirements(): array
    {
        return $this->routeRequirements;
    }

    public function getRouteTarget(): string
    {
        return $this->routeTarget;
    }
}
