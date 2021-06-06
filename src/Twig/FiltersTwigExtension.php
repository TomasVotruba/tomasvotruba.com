<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use ParsedownExtra;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FiltersTwigExtension extends AbstractExtension
{
    public function __construct(
        private ParsedownExtra $parsedownExtra
    ) {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        $twigFilters = [];
        $twigFilters[] = new TwigFilter('markdown', fn (?string $content): string => $this->parsedownExtra->parse(
            $content
        ));

        return $twigFilters;
    }
}
