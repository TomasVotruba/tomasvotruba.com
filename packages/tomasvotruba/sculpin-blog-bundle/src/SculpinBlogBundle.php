<?php

namespace TomasVotruba\SculpinBlogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use TomasVotruba\SculpinBlogBundle\DependencyInjection\Extension\SculpinBlogExtension;

final class SculpinBlogBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new SculpinBlogExtension;
    }
}
