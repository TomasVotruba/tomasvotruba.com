<?php declare(strict_types=1);

namespace TomasVotruba\Website\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $kernel = new WebsiteKernel();
        $kernel->boot();

        return $kernel->getContainer();
    }
}
