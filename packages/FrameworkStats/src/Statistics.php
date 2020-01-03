<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats;

use Nette\Utils\Strings;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;

final class Statistics
{
    public function expandDailyAverageValuesByDayCountInMonth(array $valuesByMonth): array
    {
        foreach ($valuesByMonth as $month => $averageDailyDownloads) {
            $daysInTheMonth = $this->getDaysInMonthByYearMonth($month);
            $valuesByMonth[$month] = $averageDailyDownloads * $daysInTheMonth;
        }

        return $valuesByMonth;
    }

    /**
     * E.g. 2019-12 → 31
     */
    private function getDaysInMonthByYearMonth(string $yearMonth): int
    {
        $matches = Strings::match($yearMonth, '#(?<year>\d+)\-(?<month>\d+)#');

        if (! isset($matches['month'])) {
            throw new ShouldNotHappenException();
        }

        $month = (int) $matches['month'];

        if (in_array($month, [1, 3, 5, 7, 8, 10, 12], true)) {
            return 31;
        }

        if (in_array($month, [2, 4, 6, 9, 11], true)) {
            return 30;
        }

        // @todo or 29
        return 28;
    }
}
