<?php

declare(strict_types=1);

// see https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META;

// $container->get(Type::class) → instance of "Type"
override(\Psr\Container\ContainerInterface::get(0), type(0));
override(\Symfony\Component\DependencyInjection\Container::get(0), type(0));
