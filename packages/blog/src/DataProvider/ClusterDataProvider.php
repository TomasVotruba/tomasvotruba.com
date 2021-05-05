<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\DataProvider;

use TomasVotruba\Blog\ValueObject\Cluster;
use TomasVotruba\Blog\ValueObjectFactory\ClusterFactory;

final class ClusterDataProvider
{
    public function __construct(
        private ClusterFactory $clusterFactory
    ) {
    }

    /**
     * @return Cluster[]
     */
    public function provide(): array
    {
        $clusters = [];

        $clusters[] = $this->clusterFactory->create(
            'Transforming Static Lock to Dynamic Reliable Code',
            'Static code hides dependencies, creates accidental circular calls and depends on hope. These traps usually reveal at times we have to focus on something else. This series is about transformation paths and how to do it without going crazy.',
            [276, 281, 99]
        );

        $clusters[] = $this->clusterFactory->create(
            'From YAML to PHP Symfony Configs',
            'In this series, you will learn about pros and cons of YAML and PHP configs, how to migrate and also tips. I will also show how to make your PHP configs more useful to your daily work in way you would not expect.',
            [269, 271, 275, 279]
        );

        $clusters[] = $this->clusterFactory->create(
            'Cleaning Lady Notes',
            'In this series, you can learn about my experience with legacy project migration. Tricks, tips, what works and what fucked me up. So you save some frustration, where is not needed, discover hidden shortcuts and cool tools you never saw before.',
            [267, 230, 229, 225, 104, 88]
        );

        $clusters[] = $this->clusterFactory->create(
            'Collector Pattern, The Shortcut Hack to SOLID Code',
            "Collector is the one pattern that will help your code exponentially. It's super easy to start using in every level of application. Stop learning 5 SOLID principles theory and start using it in your code today.",
            [114, 158, 36, 133]
        );

        $clusters[] = $this->clusterFactory->create(
            'Monorepo: From Zero to Hero',
            "What is monorepo? How can you use it to speed up your packages' and projects' development? How to run your own monorepo in 10 minutes?",
            [287, 286, 283, 256, 223, 69, 25, 82, 124, 138, 143, 160, 161, 182]
        );

        $clusters[] = $this->clusterFactory->create(
            'Coding Standards Kata',
            'How to start coding standards and use them to their fullest potential?',
            [37, 108, 111, 112, 48, 46, 47, 87, 107]
        );

        $clusters[] = $this->clusterFactory->create(
            'Books in a Post',
            'Reading Books is what separates programmers from craftsmen. I love books, because they open for deep conversation that last hours and hours. Care for ittle intro first?',
            [65, 149, 115, 91, 56]
        );

        $clusters[] = $this->clusterFactory->create(
            'From Nette to Symfony',
            'How to migrate from Nette to Symfony?',
            [188, 192, 193, 186, 185, 171, 120, 197],
        );

        $clusters[] = $this->clusterFactory->create(
            'Master PHP CLI Apps with Symfony',
            "Do you want to write CLI Apps in PHP like a boss but you never did that before? Or do you look for tips to improve your CLI project? This will help you write better and cleaner code no matter level you're on.",
            [128, 103, 109, 105, 129, 137]
        );

        return $clusters;
    }
}
