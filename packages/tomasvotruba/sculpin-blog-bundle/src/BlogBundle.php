<?php

namespace TomasVotruba\SculpinBlogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use TomasVotruba\SculpinBlogBundle\DependencyInjection\Extension\BlogExtension;

final class BlogBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return new BlogExtension;
    }
}
