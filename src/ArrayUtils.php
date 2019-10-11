<?php

declare(strict_types=1);

namespace TomasVotruba\Website;

final class ArrayUtils
{
    public function sortDataByKey(array $data, string $key): array
    {
        usort($data, function (array $firstItem, array $secondItem) use ($key): int {
            return $secondItem[$key] <=> $firstItem[$key];
        });

        return $data;
    }

    public function getArrayKeyAverage(array $packagesData, string $key): float
    {
        $total = [];
        foreach ($packagesData as $packageData) {
            $total[] = $packageData[$key];
        }

        $average = array_sum($total) / (count($total) ?: 1);

        return round($average, 1);
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
