<?php

declare(strict_types=1);

namespace TomasVotruba\Website\HttpKernel;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\SymfonyStaticDumper\ValueObject\SymfonyStaticDumperConfig;

final class TomasVotrubaKernel extends Kernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        $loader->load(SymfonyStaticDumperConfig::FILE_PATH);
    }

    protected function configureRoutes(RoutingConfigurator $routingConfigurator): void
    {
        $routingConfigurator->import(__DIR__ . '/../../config/routes.php');
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
