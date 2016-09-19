<?php

namespace TomasVotruba\SculpinBlogBundle\Twig;

use Sculpin\Contrib\ProxySourceCollection\ProxySourceItem;
use Twig_Extension;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;

final class SculpinBlogExtension extends Twig_Extension
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
            }),
            new Twig_SimpleFilter('githubEditPostUrl', function ($post) {
                return
                    'https://github.com/TomasVotruba/tomasvotruba.cz/edit/master/source/_posts/'.
                    $this->getFileNameFromPost($post);
            })
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * @return string|void
     */
    private function readTimeInMinutes(string $text = null, string $lang = null)
    {
        if ($text === null) {
            return;
        }

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

        return $minutesCount . ' ' . $minutesLocalized . ' ' . ($lang === 'en' ? 'of reading' : 'čtení');
    }

    private function wordCount(string $text = null) : int
    {
        $text = strip_tags($text);
        $wordCount = count(explode(' ', $text));
        return $wordCount;
    }

    /**
     * @param array|ProxySourceItem $post
     */
    private function getFileNameFromPost($post) : string
    {
        if (isset($post['next_post'])) {
            $nextPost = $post['next_post'];
            if ($nextPost instanceof ProxySourceItem) {
                return $nextPost->previousItem()['filename'];
            }
        }

        if (isset($post['previous_post'])) {
            $nextPost = $post['previous_post'];
            if ($nextPost instanceof ProxySourceItem) {
                return $nextPost->nextItem()['filename'];
            }
        }

        return '';
    }
}
