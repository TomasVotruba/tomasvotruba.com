<?php

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;
use TomasVotruba\SculpinBlogBundle\SculpinBlogBundle;
use TomasVotruba\SculpinTranslationBundle\SculpinTranslationBundle;

class SculpinKernel extends AbstractKernel
{
    /**
     * {@inheritdoc}
     */
    protected function getAdditionalSculpinBundles()
    {
        return [
            SculpinBlogBundle::class,
            SculpinTranslationBundle::class
        ];
    }
}
