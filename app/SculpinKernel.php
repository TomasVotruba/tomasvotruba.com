<?php

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;
use TomasVotruba\SculpinBlogBundle\SculpinBlogBundle;
use TomasVotruba\SculpinTranslationBundle\SculpinTranslationBundle;

class SculpinKernel extends AbstractKernel
{
    protected function getAdditionalSculpinBundles() : array
    {
        return [
            SculpinBlogBundle::class,
            SculpinTranslationBundle::class
        ];
    }
}
