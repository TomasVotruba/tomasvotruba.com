<?php

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;
use TomasVotruba\SculpinBlogBundle\BlogBundle;

class SculpinKernel extends AbstractKernel
{
    /**
     * {@inheritdoc}
     */
    protected function getAdditionalSculpinBundles()
    {
        return [
            BlogBundle::class
        ];
    }
}
