<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Website\ValueObject\Option;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class GlobalVariablesTwigExtension extends AbstractExtension implements GlobalsInterface
{
    private ParameterProvider $parameterProvider;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @see https://stackoverflow.com/a/42540337/1348344
     * @return array<string, mixed>
     */
    public function getGlobals(): array
    {
        $contributors = $this->parameterProvider->provideArrayParameter(Option::CONTRIBUTORS);
        $clusters = $this->parameterProvider->provideArrayParameter(Option::CLUSTERS);

        return [
            'contributors_count' => count($contributors),
            'clusters' => $clusters,
        ];
    }
}
