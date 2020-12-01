<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Blog\Repository\ClusterRepository;
use TomasVotruba\Website\ValueObject\Option;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class GlobalVariablesTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private ParameterProvider $parameterProvider,
        private ClusterRepository $clusterRepository,
    )
    {
    }

    /**
     * @see https://stackoverflow.com/a/42540337/1348344
     * @return array<string, mixed>
     */
    public function getGlobals(): array
    {
        $contributors = $this->parameterProvider->provideArrayParameter(Option::CONTRIBUTORS);
        $clusters = $this->clusterRepository->getClusters();

        return [
            'contributors_count' => count($contributors),
            'clusters' => $clusters,
        ];
    }
}
