<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;

final class GlobalVariablesTwigExtension extends AbstractExtension
{
    public function __construct(Environment $environment, array $clusters, array $contributors = [])
    {
        $environment->addGlobal('contributors_count', count($contributors));

        $environment->addGlobal('google_analytics_tracking_id', 'UA-46082345-1');

        $environment->addGlobal('clusters', $clusters);

        $environment->addGlobal('site_title', 'Tomas Votruba');

        $environment->addGlobal('disqus_shortname', 'itsworthsharing');

        $environment->addGlobal(
            'github_repository_tests_directory',
            'https://github.com/TomasVotruba/tomasvotruba.com/tree/master/tests'
        );
    }
}
