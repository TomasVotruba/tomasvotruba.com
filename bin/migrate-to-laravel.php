<?php

use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

require __DIR__ . '/../vendor/autoload.php';

// @todo replace regex in twigs

// @todo create rector rules
// @todo ask GPT to help with that :)
// before + after

// 1. convert twig to blade

final class TwigToBladeConverter
{
    private const TWIG_TO_BLADE_REPLACE_REGEXES = [
        '#{% if (?<condition>.*?) %}#' => '@if ($1)',
        '#{% for (?<singular>.*?) in (?<pluar>.*?) %}#' => '@foreach ($2 as $1)',
    ];

    public function run(string $templatesDirectory): void
    {
        $twigFilePaths = $this->findTwigFilePaths($templatesDirectory);

        foreach ($twigFilePaths as $twigFilePath) {
            $bladeFilePath = substr(realpath($twigFilePath), 0, -5) . '.blade.php';

            $templateContents = \Nette\Utils\FileSystem::read($twigFilePath);

            foreach (self::TWIG_TO_BLADE_REPLACE_REGEXES as $twigRegex => $bladeReplacement) {
                $templateContents = \Nette\Utils\Strings::replace($templateContents, $twigRegex, $bladeReplacement);
            }

            dump($templateContents);

            // @todo differ?
        }
    }

    /**
     * @return string[]
     */
    private function findTwigFilePaths(string $templatesDirectory): array
    {
        $twigFinder = Finder::create()
              ->files()
              ->name('*.twig')
              ->in($templatesDirectory);

        $twigFilePaths = array_keys(iterator_to_array($twigFinder->getIterator()));
        Assert::allString($twigFilePaths);

        return $twigFilePaths;
    }
}


$twigToBladeConverter = new TwigToBladeConverter();
$twigToBladeConverter->run(__DIR__ . '/../templates');
