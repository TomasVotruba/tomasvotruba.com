<?php

namespace BlogDomainBundle\DependencyInjection;

use Symfony\Bundle\DebugBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class BlogDomainExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration;
        $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->prependExtensionConfig('framework', [
            'router' => [
                'resource' => '%kernel.root_dir%/config/routing.php',
            ]
        ]);

        $containerBuilder->prependExtensionConfig('twig', [
            'paths' => [
                '%kernel.root_dir%/../src/BlogDomainBundle/Resources/views' => 'BlogDomainBundle'
            ]
        ]);
    }
}
