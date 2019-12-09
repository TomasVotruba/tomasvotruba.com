<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TalksCountTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): iterable
    {
        yield new TwigFilter('talks_count', function (array $talksByTopic): int {
            $count = 0;
            foreach ($talksByTopic as $talks) {
                $count += count($talks['events']);
            }

            return $count;
        });
    }
}
