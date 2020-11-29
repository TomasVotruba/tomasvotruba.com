<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TomasVotruba\Website\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // @todo use value objects here :)
    // new Cluster()...
    $parameters->set(Option::CLUSTERS, [
        [
            'title' => 'From YAML to PHP Symfony Configs',
            'post_ids' => [269, 271, 275, 279],
            'description' => 'In this series, you will learn about pros and cons of YAML and PHP configs, how to migrate and also tips. I will also show how to make your PHP configs more useful to your daily work in way you would not expect.',
        ],
        [
            'title' => 'Cleaning Lady Notes',
            'post_ids' => [267, 230, 229, 225, 104, 88],
            'description' => 'In this series, you can learn about my experience with legacy project migration. Tricks, tips, what works and what fucked me up. So you **save some frustration, where is not needed, discover hidden shortcuts and cool tools you never saw before.',
        ],
        [
            'title' => 'Collector Pattern, The Shortcut Hack to SOLID Code',
            'post_ids' => [114, 158, 36, 133],
            'description' => 'Collector is the one pattern that will help your code exponentially. It\'s super easy to start using in every level of application. Stop learning 5 SOLID principles theory and start using it in your code today.',
        ],
        [
            'title' => 'Monorepo: From Zero to Hero',
            'post_ids' => [288, 287, 286, 283, 256, 223, 69, 25, 82, 124, 138, 143, 160, 161, 182],
            'description' => 'What is monorepo? How can you use it to speed up your packages\' and projects\' development? How to run your own monorepo in 10 minutes?',
        ],
        [
            'title' => 'Coding Standards Kata',
            'post_ids' => [37, 108, 111, 112, 48, 46, 47, 87, 107],
            'description' => 'How to start coding standards and use them to their fullest potential?',
        ],
        [
            'title' => 'Books in a Post',
            'post_ids' => [65, 149, 115, 91, 56],
            'description' => 'Reading Books is what separates programmers from craftsmen. I love books, because they open for deep conversation that last hours and hours. Care for little intro first?',
        ],
        [
            'title' => 'From Nette to Symfony',
            'post_ids' => [188, 192, 193, 186, 185, 171, 120, 197],
            'description' => 'How to migrate from Nette to Symfony?',
        ],
        [
            'title' => 'Master PHP CLI Apps with Symfony',
            'post_ids' => [128, 103, 109, 105, 129, 137],
            'description' => 'Do you want to write CLI Apps in PHP like a boss but you never did that before? Or do you look for tips to improve your CLI project? This will help you write better and cleaner code no matter level you\'re on.',
        ],
    ]);
};
