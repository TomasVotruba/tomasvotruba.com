<?php

namespace TomasVotruba\SculpinTranslationBundle\Filesystem;

use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;

final class ResourceFinder
{
    /**
     * @param string $directory
     * @return array
     */
    public function findInDirectory($directory)
    {
        $finder = new Finder();
        $resources = [];

        foreach ($finder->name('*.*.yml')->in($directory) as $file) {
            /** @var \SplFileInfo $file */
            if (!$m = Strings::match(
                $file->getFilename(),
                '~^(?P<domain>.*?)\.(?P<locale>[^\.]+)\.(?P<format>[^\.]+)$~')
            ) {
                continue;
            }

            $resources[] = [
                'format' => $m['format'],
                'pathname' => $file->getPathname(),
                'locale' => $m['locale'],
                'domain' => $m['domain'],
            ];
        }

        return $resources;
    }
}
