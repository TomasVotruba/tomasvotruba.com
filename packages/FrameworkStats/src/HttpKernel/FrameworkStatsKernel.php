<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class FrameworkStatsKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yaml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/framework_stats';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/framework_stats_log';
    }
}
