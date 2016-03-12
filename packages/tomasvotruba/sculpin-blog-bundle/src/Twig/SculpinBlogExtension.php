<?php

namespace TomasVotruba\SculpinBlogBundle\Twig;

use Twig_Environment;
use Twig_ExtensionInterface;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;

final class SculpinBlogExtension implements Twig_ExtensionInterface
{
    public function __construct(Twig_Loader_Chain $twigLoaderChain)
    {
        $twigLoaderChain->addLoader(
            new Twig_Loader_Filesystem([__DIR__ . '/../Resources/templates'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('readTimeInMinutes', function ($text, $lang) {
                return $this->readTimeInMinutes($text, $lang);
            })
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Twig_Environment $environment)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * @param int $text
     * @param string $lang
     * @return int
     */
    private function readTimeInMinutes($text, $lang)
    {
        $wordCount = $this->wordCount($text);
        $minutesCount = ceil($wordCount/260);

        switch ($minutesCount) {
		    case 1:
                $minutesLocalized = $lang === 'en' ? 'minute' : 'minuta';
                break;
            case 2:
            case 3:
            case 4:
                $minutesLocalized = $lang === 'en' ? 'minutes' : 'minuty';
                break;
            default:
                $minutesLocalized = $lang === 'en' ? 'minutes' : 'minut';
                break;
        }

        return $minutesCount . ' ' . $minutesLocalized . ' '
            . ($lang === 'en' ? 'of reading' : 'čtení');
    }

    /**
     * @param string $text
     * @return int
     */
    private function wordCount($text)
    {
        $text = strip_tags($text);
        $wordCount = count(explode(' ', $text));
        return $wordCount;
    }
}
