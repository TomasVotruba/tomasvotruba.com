<?php

namespace AppBundle\Contract\Utils;

interface MarkdownInterface
{
    /**
     * @param string $text
     * @return string
     */
    public function toHtml($text);
}
