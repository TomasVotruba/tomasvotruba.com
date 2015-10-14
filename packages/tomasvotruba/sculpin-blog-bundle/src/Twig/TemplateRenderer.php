<?php

namespace TomasVotruba\SculpinBlogBundle\Twig;

use InvalidArgumentException;
use SplFileInfo;
use TomasVotruba\SculpinBlogBundle\Contract\Twig\TemplateRendererInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

final class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($file, $arguments = [])
    {
        if ( ! file_exists($file)) {
            throw new InvalidArgumentException(
                sprintf('File "%s" was not found.', $file)
            );
        }

        $splFile = new SplFileInfo($file);
        $dirname = dirname($file);
        $filename = $splFile->getBasename();

        $loader = new Twig_Loader_Filesystem([$dirname]);
        $twig = new Twig_Environment($loader);
        return $twig->render($filename);
    }
}
