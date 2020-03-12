<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats;

use Nette\Utils\DateTime;

final class Statistics
{
    /**
     * @param int[] $dailyAverageByYearMonth
     * @return int[]
     */
    public function expandDailyAverageToMonthTotal(array $dailyAverageByYearMonth): array
    {
        foreach ($dailyAverageByYearMonth as $yearMonth => $dailyAverage) {
            $daysInTheMonth = $this->getDaysInMonthByYearMonth($yearMonth);
            $dailyAverageByYearMonth[$yearMonth] = $dailyAverage * $daysInTheMonth;
        }

        return $dailyAverageByYearMonth;
    }

    /**
     * E.g. 2019-12 → 31
     */
    private function getDaysInMonthByYearMonth(string $yearMonth): int
    {
        $dateTime = DateTime::from($yearMonth);

        return (int) $dateTime->format('t');
    }
}
