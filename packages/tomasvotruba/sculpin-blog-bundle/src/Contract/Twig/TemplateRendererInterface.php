<?php

namespace TomasVotruba\SculpinBlogBundle\Contract\Twig;

interface TemplateRendererInterface
{
    /**
     * @param string $file
     * @param array $arguments
     */
    function render($file, $arguments = []);
}
