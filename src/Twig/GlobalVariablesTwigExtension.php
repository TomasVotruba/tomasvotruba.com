<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Twig\Environment;
use Twig\Extension\AbstractExtension;

final class GlobalVariablesTwigExtension extends AbstractExtension
{
    public function __construct(Environment $environment, ParameterProvider $parameterProvider)
    {
        $contributors = $parameterProvider->provideArrayParameter('contributors');
        $clusters = $parameterProvider->provideArrayParameter('clusters');

        $contributorCount = count($contributors);

        $environment->addGlobal('contributors_count', $contributorCount);
        $environment->addGlobal('clusters', $clusters);
    }
}
