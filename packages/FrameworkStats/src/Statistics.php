<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;

final class Statistics
{
    public function expandDailyAverageToMonthTotal(array $valuesByMonth): array
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

        $dateTime = DateTime::from($month);

        return (int) $dateTime->format('t');
    }
}
