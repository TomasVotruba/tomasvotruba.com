<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class NumberTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        yield new TwigFilter('millions', function (int $number): string {
            if ($number > 10 ** 5) {
                return $this->formatNumber($number / (10 ** 6)) . ' M';
            }

            if ($number > 10 ** 2) {
                return $this->formatNumber($number / (10 ** 3)) . ' K';
            }

            return $this->formatNumber($number);
        });
    }

    /**
     * @param float|int $number
     */
    private function formatNumber($number): string
    {
        return number_format($number, 1, '.', ' ');
    }
}
