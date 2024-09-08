<?php

declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\Tool;

final class ToolRepository
{
    /**
     * @return Tool[]
     */
    public function fetchAll(): array
    {
        return [new Tool(
            'Easy Coding Standard',
            'First week when you come to a new project',
            'Adds advanced coding standard fast',
            'https://github.com/easy-coding-standard/easy-coding-standard',
            'https://tomasvotruba.com/blog/introducing-up-to-16-times-faster-easy-coding-standard',
            'composer require symplify/easy-coding-standard --dev',
            [
                'First run or dry-run' => 'vendor/bin/ecs',
                'Fix coding standdard' => 'vendor/bin/ecs --fix',
            ],
        ), new Tool(
            'Type Coverage',
            'When you reach PHP 7.0+ with scalar types',
            'Helps you add type declarations 1 % at a time',
            'https://github.com/TomasVotruba/type-coverage',
            'https://tomasvotruba.com/blog/how-to-measure-your-type-coverage',
            'composer require tomasvotruba/type-coverage --dev',
            [],
            true,
            <<<'PHPSTAN'
parameters:
    type_coverage:
        return: 5
        param: 5
        # enable on PHP 7.4+
        # property: 5
        # enable on PHP 8.3+
        # constant: 5
PHPSTAN
        ), new Tool(
            'Class Leak',
            'When you reach PHPStan level 2',
            'Spots unused classes',
            'https://github.com/TomasVotruba/class-leak',
            'https://tomasvotruba.com/blog/how-to-avoid-maintaining-classes-you-dont-use',
            'composer require tomasvotruba/class-leak --dev',
            [
                'Detect unused classes' => 'vendor/bin/class-leak check /src /tests',
            ]
        ), new Tool(
            'Unused public',
            'When you reach PHPStan level 3/4',
            'Removes unused public code you maintain',
            'https://github.com/TomasVotruba/unused-public',
            'https://tomasvotruba.com/blog/can-phpstan-find-dead-public-methods/',
            'composer require tomasvotruba/unused-public --dev',
            [],
            true,
            <<<'PHPSTAN'
parameters:
    unused_public:
        constants: true
        # handle one by one
        # properties: true
        # methods: true
PHPSTAN
        ), new Tool(
            'Swiss Knife',
            'When you reach PHPStan level 3/4',
            'Finalizes classes without children, makes class constants private and more',
            'https://github.com/rectorphp/swiss-knife',
            'https://tomasvotruba.com/blog/cool-features-of-swiss-knife',
            'composer require rector/swiss-knife --dev',
            [
                'Finalize classes without children' => 'vendor/bin/swiss-knife finalize-classes /src /tests',
                'Privatize local class constants' => 'vendor/bin/swiss-knife privatize-constants /src /tests',
            ]
        ), new Tool(
            'Type Perfect',
            'When you reach PHPStan level 6',
            'Help you remove mixed types from obviously known code',
            'https://github.com/rectorphp/type-perfect',
            'https://getrector.com/blog/introducing-type-perfect-for-extra-safety',
            'composer require rector/type-perfect --dev',
            [],
            true,
            <<<'PHPSTAN'
parameters:
    type_perfect:
        null_over_false: true
        # enable one a by one
        # no_mixed: true
        # narrow_param: true
        # narrow_return: true
PHPSTAN
        ), new Tool(
            'Config Transformer',
            'When you have Symfony configs in YAML',
            'Converts YAML configs to PHP for you',
            'https://github.com/symplify/config-transformer',
            'https://tomasvotruba.com/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/',
            'composer require symplify/config-transformer --dev',
            [
                'Transform' => 'vendor/bin/config-transformer convert /config',
            ]
        )];
    }
}
