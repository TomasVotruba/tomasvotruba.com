<?php declare(strict_types=1);

namespace TomasVotruba\Website;

final class Statistics
{
    /**
     * @param int[] $values
     */
    public function resolveTotal(array $values, int $months, int $offset): int
    {
        $total = 0;

        $end = $offset + $months;

        for ($i = $offset; $i < $end; $i++) {
            if (! isset($values[$i])) {
                break;
            }

            // 30 for compensating average of month
            $total += $values[$i] * 30;
        }

        return $total;
    }
}
