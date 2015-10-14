<?php

namespace TomasVotruba\SculpinBlogBundle\Twig;

use Sculpin\Contrib\ProxySourceCollection\ProxySourceItem;
use TomasVotruba\SculpinBlogBundle\Contract\Twig\TemplateRendererInterface;
use Twig_Extension;
use Twig_SimpleFilter;

final class BlogTwigExtension extends Twig_Extension
{
    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    /**
     * @var string
     */
    private $disqusId;

    /**
     * @param TemplateRendererInterface $templateRenderer
     * @param string $disqusId
     */
    public function __construct(TemplateRendererInterface $templateRenderer, $disqusId)
    {
        $this->templateRenderer = $templateRenderer;
        $this->disqusId = $disqusId;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('singlePost', function (ProxySourceItem $post, $areCommentsAllowed = false) {
                return $this->templateRenderer->render(__DIR__ . '/templates/post.twig', [
                    'post' => $post,
                    'areCommentsAllowed' => $areCommentsAllowed
                ]);
            })
        ];
    }

}
