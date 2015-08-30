<?php

namespace App;

use AppBundle;
use BlogDomainBundle\BlogDomainBundle;
use Doctrine;
use InteroperabilityAdapter\Symfony\InteroperabilityAdapterBundle;
use Sensio;
use Symfony;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symnedi\AutowiringBundle\SymnediAutowiringBundle;

class AppKernel extends Kernel
{
    /**
     * @return Bundle[]
     */
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle,
            new Symfony\Bundle\TwigBundle\TwigBundle,
            new Symfony\Bundle\AsseticBundle\AsseticBundle,
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle,
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle,
            new BlogDomainBundle(),
            new SecurityBundle(),
            new SymnediAutowiringBundle(),
            new InteroperabilityAdapterBundle()
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle;
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle;
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->rootDir . '/../var/cache/' . $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->rootDir . '/../var/logs';
    }
}
