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

    public function getArrayKeySum(array $array, string $key): int
    {
        $total = 0;
        foreach ($array as $item) {
            $total += (int) $item[$key];
        }

        return $total;
    }
}
