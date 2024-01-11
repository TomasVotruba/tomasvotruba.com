<?php

declare(strict_types=1);

// @see https://github.com/thephpleague/commonmark

use League\CommonMark\GithubFlavoredMarkdownConverter;

function markdown(string $contents): Stringable
{
    $markdownConverter = new GithubFlavoredMarkdownConverter([
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);

    return $markdownConverter->convert($contents);
}
