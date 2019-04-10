<?php declare(strict_types=1);

namespace TomasVotruba\Website;

final class ArrayUtils
{
    /**
     * @param mixed[] $packagesData
     */
    public function getArrayKeyAverage(array $packagesData, string $key): float
    {
        $total = [];
        foreach ($packagesData as $packageData) {
            $total[] = $packageData[$key];
        }

        $average = array_sum($total) / (count($total) ?: 1);

        return round($average, 2);
    }

    /**
     * @param mixed[] $array
     */
    public function getArrayKeySum(array $array, string $key): int
    {
        $total = 0;
        foreach ($array as $item) {
            $total += (int) $item[$key];
        }

        return $total;
    }
}
