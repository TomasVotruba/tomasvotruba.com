<?php

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;
use TomasVotruba\SculpinBlogBundle\SculpinBlogBundle;

class SculpinKernel extends AbstractKernel
{
    /**
     * {@inheritdoc}
     */
    protected function getAdditionalSculpinBundles()
    {
        return [
            SculpinBlogBundle::class
        ];
    }
}
