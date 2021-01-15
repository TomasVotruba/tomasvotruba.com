<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use ParsedownExtra;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class MarkdownTwigExtension extends AbstractExtension
{
    public function __construct(
        private ParsedownExtra $parsedownExtra
    ) {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): iterable
    {
        yield new TwigFilter('markdown', fn (?string $content): string => $this->parsedownExtra->parse($content));
    }
}
