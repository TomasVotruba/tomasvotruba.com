<?php

namespace TomasVotruba\SculpinBlogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('sculpin_blog');
        $rootNode
            ->children()
                ->scalarNode('disqus_id')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}
