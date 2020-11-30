<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TomasVotruba\Website\ValueObject\Cluster;
use TomasVotruba\Website\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // @todo use value objects here :)
    // new Cluster()...
    $clusters = [
        new Cluster(
            'From YAML to PHP Symfony Configs',
            [269, 271, 275, 279],
            'In this series, you will learn about pros and cons of YAML and PHP configs, how to migrate and also tips. I will also show how to make your PHP configs more useful to your daily work in way you would not expect.'
        ),
        new Cluster(
            'Cleaning Lady Notes',
            [267, 230, 229, 225, 104, 88],
            'In this series, you can learn about my experience with legacy project migration. Tricks, tips, what works and what fucked me up. So you **save some frustration, where is not needed, discover hidden shortcuts and cool tools you never saw before.',
        ),
        new Cluster(
            'Collector Pattern, The Shortcut Hack to SOLID Code',
            [114, 158, 36, 133],
            'Collector is the one pattern that will help your code exponentially. It\'s super easy to start using in every level of application. Stop learning 5 OLID principles theory and start using it in your code today.',
        ),
        new Cluster(
            'Monorepo: From Zero to Hero',
            [288, 287, 286, 283, 256, 223, 69, 25, 82, 124, 138, 143, 160, 161, 182],
            'What is monorepo? How can you use it to speed up your packages\' and projects\' development? How to run your own monorepo in 10 minutes?',
        ),
        new Cluster(
            'Coding Standards Kata',
            [37, 108, 111, 112, 48, 46, 47, 87, 107],
            'How to start coding standards and use them to their fullest potential?',
        ),
        new Cluster(
            'Books in a Post',
            [65, 149, 115, 91, 56],
            'Reading Books is what separates programmers from craftsmen. I love books, because they open for deep conversation that last hours and hours. Care for ittle intro first?',
        ),
        new Cluster(
            'From Nette to Symfony',
            [188, 192, 193, 186, 185, 171, 120, 197],
            'How to migrate from Nette to Symfony?',
        ),
        new Cluster(
            'Master PHP CLI Apps with Symfony',
            [128, 103, 109, 105, 129, 137],
            'Do you want to write CLI Apps in PHP like a boss but you never did that before? Or do you look for tips to improve your CLI project? This will help you write better and cleaner code no matter level you\'re on.',
        )
    ];
    $parameters->set(Option::CLUSTERS, $clusters);
};
