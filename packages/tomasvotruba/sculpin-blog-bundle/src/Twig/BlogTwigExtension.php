<?php

namespace TomasVotruba\SculpinBlogBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

final class BlogTwigExtension extends Twig_Extension
{

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('price', function ($value) {
                // it works!
                echo 'YEAH';
            }),
        ];
    }

}
