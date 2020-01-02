<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats;

use Nette\Utils\Strings;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;

final class Statistics
{
    /**
     * @param int[] $values
     */
    public function resolveTotal(array $values, int $months, int $offset): int
    {
        $total = 0;

        $end = $offset + $months;

        $counter = 0;
        foreach ($values as $month => $averageDailyDownloads) {
            ++$counter;

            $daysInTheMonth = $this->getDaysInMonthByYearMonth($month);
            $total += $averageDailyDownloads * $daysInTheMonth;

            if ($counter > $end) {
                break;
            }
        }

        return $total;
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
