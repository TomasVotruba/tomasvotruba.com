<?php

declare(strict_types=1);

namespace TomasVotruba\Website;

use TomasVotruba\Website\Contract\FrameworkStats\LastYearTrendAwareInterface;

final class ArrayUtils
{
    /**
     * @param LastYearTrendAwareInterface[] $data
     * @return LastYearTrendAwareInterface[]
     */
    public function sortArrayByLastYearTrend(array $data): array
    {
        usort($data, function (LastYearTrendAwareInterface $firstItem, LastYearTrendAwareInterface $secondItem): int {
            return $secondItem->getLastYearTrend() <=> $firstItem->getLastYearTrend();
        });

        return $data;
    }

    public function getArrayKeySum(array $array, string $key): int
    {
        $total = 0;
        foreach ($array as $item) {
            $total += (int) $item[$key];
        }

        return $total;
    }
}
