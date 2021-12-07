<?php

declare(strict_types=1);

namespace TomasVotruba\Blog;

use Symplify\SmartFileSystem\SmartFileSystem;

final class PostSnippetDecorator
{
    /**
     * @var array<string, string>
     */
    private const PLACEHOLDER_TO_SNIPPET_FILE_MAP = [
        '[link_rector_book]' => __DIR__ . '/../../../templates/blog/post_snippet/link_rector_book.html',
    ];

    public function __construct(
        private readonly SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * Useful for html complex placeholder replacements in the posts
     */
    public function decorateHtmlContent(string $htmlContent): string
    {
        foreach (self::PLACEHOLDER_TO_SNIPPET_FILE_MAP as $placeholder => $snippetFile) {
            $htmlSnippet = $this->smartFileSystem->readFile($snippetFile);

            $htmlContent = str_replace($placeholder, $htmlSnippet, $htmlContent);
        }

        return $htmlContent;
    }
}
