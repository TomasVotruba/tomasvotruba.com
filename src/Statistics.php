<?php declare(strict_types=1);

namespace TomasVotruba\Website;

final class Statistics
{
    public function resolveTrend(array $values, int $trendSize): ?float
    {
        $halfDuration = $trendSize / 2;

        $firstHalf = $this->countFirstHalf($values, $halfDuration);
        $secondHalf = $this->countSecondHalf($values, $trendSize, $halfDuration);

        if ($secondHalf === null || $secondHalf === 0) {
            return null;
        }

        $trend = $firstHalf / $secondHalf;

        return round(($trend - 1) * 100, 2);
    }

    /**
     * @param int[] $values
     */
    public function resolveTotal(array $values, int $months): int
    {
        $total = 0;

        for ($i = 0; $i < $months; $i++) {
            if (! isset($values[$i])) {
                break;
            }

            // 30 for compensating average of month
            $total += $values[$i] * 30;
        }

        return $total;
    }

    private function countFirstHalf(array $values, int $halfDuration): ?int
    {
        $firstHalf = 0;
        for ($i = 1; $i <= $halfDuration; $i++) {
            if (! isset($values[$i])) {
                // unable to calculate
                return null;
            }

            $firstHalf += $values[$i];
        }

        return (int) $firstHalf;
    }

    private function countSecondHalf(array $values, int $trendSize, int $halfDuration): ?int
    {
        $secondHalf = 0;
        for ($i = $halfDuration + 1; $i <= $trendSize; $i++) {
            if (! isset($values[$i])) {
                // unable to calculate
                return null;
            }

            $secondHalf += $values[$i];
        }

        return (int) $secondHalf;
    }
}
