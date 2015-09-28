<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Twig;

use AppBundle\Contract\Utils\MarkdownInterface;
use Twig_Extension;
use Twig_SimpleFilter;

final class MarkdownFilterExtension extends Twig_Extension
{
    /**
     * @var MarkdownInterface
     */
    private $parser;

    public function __construct(MarkdownInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter(
                'md2html',
                function ($content) {
                    return $this->parser->toHtml($content);
                },
                [ 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }
}
