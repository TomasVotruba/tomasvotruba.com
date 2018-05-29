<?php declare(strict_types=1);

namespace TomasVotruba\Website\Posts\Year2018\ConsoleDI\App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use TomasVotruba\Website\Posts\Year2018\ConsoleDI\DependencyInjection\CompilerPass\CollectCommandsCompilerPass;

final class AppKernel extends Kernel
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
        $loader->load(__DIR__ . '/../config/services.yml');
    }

    /**
     * Unique cache path for this Kernel
     */
    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_tomas_votruba_blog_cache' . md5(self::class);
    }

    /**
     * Unique logs path for this Kernel
     */
    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_tomas_votruba_blog_log' . md5(self::class);
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectCommandsCompilerPass());
    }
}
