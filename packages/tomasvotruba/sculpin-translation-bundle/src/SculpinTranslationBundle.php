<?php

namespace TomasVotruba\SculpinTranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use TomasVotruba\SculpinTranslationBundle\DependencyInjection\Extension\SculpinTranslationExtension;

final class SculpinTranslationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new SculpinTranslationExtension();
    }
}
