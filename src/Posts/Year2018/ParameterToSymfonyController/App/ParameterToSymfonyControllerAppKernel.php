<?php declare(strict_types=1);

namespace TomasVotruba\Website\Posts\Year2018\ParameterToSymfonyController\App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class ParameterToSymfonyControllerAppKernel extends Kernel
{
    public function __construct()
    {
        // these values allows container rebuild when config changes
        parent::__construct('dev', true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_tomas_votruba_blog_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_tomas_votruba_blog_log';
    }
}
