<?php

declare(strict_types=1);

namespace TomasVotruba\Blog;

use Nette\Utils\FileSystem;

final class PostSnippetDecorator
{
    /**
     * @var array<string, string>
     */
    private const PLACEHOLDER_TO_SNIPPET_FILE_MAP = [
        '[link_rector_book]' => __DIR__ . '/../../../templates/blog/post_snippet/link_rector_book.html',
    ];

    /**
     * Useful for html complex placeholder replacements in the posts
     */
    public function decorateHtmlContent(string $htmlContent): string
    {
        foreach (self::PLACEHOLDER_TO_SNIPPET_FILE_MAP as $placeholder => $snippetFile) {
            $htmlSnippet = FileSystem::read($snippetFile);

            $htmlContent = str_replace($placeholder, $htmlSnippet, $htmlContent);
        }

        return $htmlContent;
    }
}
