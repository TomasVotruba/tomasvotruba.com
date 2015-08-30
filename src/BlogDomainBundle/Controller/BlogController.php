<?php

/*
 * This file is part of Tomasvotruba.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace BlogDomainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

final class BlogController
{
    /**
     * @var EngineInterface
     */
    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function indexAction()
    {
        // todo: find all posts
        return $this->templating->renderResponse('@BlogDomainBundle/blog/index.html.twig', [
            'posts' => []
        ]);
    }
}
