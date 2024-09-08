<?php

declare(strict_types=1);

// @see https://github.com/thephpleague/commonmark

use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Util\HtmlFilter;

function markdown(string $contents): Stringable
{
    $markdownConverter = new GithubFlavoredMarkdownConverter([
        'html_input' => HtmlFilter::ALLOW,
        'allow_unsafe_links' => false,
    ]);

    return $markdownConverter->convert($contents);
}
