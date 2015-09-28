<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Utils;

use AppBundle\Contract\Utils\MarkdownInterface;
use Parsedown;

/**
 * This class is a light interface between an external Markdown parser library
 * and the application. It's generally recommended to create these light interfaces
 * to decouple your application from the implementation details of the third-party library.
 */
final class Markdown implements MarkdownInterface
{
    /**
     * @var Parsedown
     */
    private $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml($text)
    {
        return $this->parser->text($text);
    }
}
