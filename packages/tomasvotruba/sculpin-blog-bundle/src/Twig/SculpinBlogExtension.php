<?php

namespace TomasVotruba\SculpinBlogBundle\Twig;

use Twig_Environment;
use Twig_ExtensionInterface;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;

final class SculpinBlogExtension implements Twig_ExtensionInterface
{
    public function __construct(Twig_Loader_Chain $twigLoaderChain)
    {
        $twigLoaderChain->addLoader(
            new Twig_Loader_Filesystem([__DIR__ . '/../Resources/templates'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Twig_Environment $environment)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }
}
