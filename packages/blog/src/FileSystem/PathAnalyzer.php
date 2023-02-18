<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\FileSystem;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

final class PathAnalyzer
{
    /**
     * @see https://regex101.com/r/kHFrUl/1
     * @var string
     */
    private const DATE_REGEX = '(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})';

    /**
     * @see https://regex101.com/r/1XjsjR/1/
     * @var string
     */
    private const NAME_REGEX = '(?<name>[\w\d-]*)';

    public function resolveDateTime(string $filePath): DateTime
    {
        $match = Strings::match($filePath, '#' . self::DATE_REGEX . '#');
        if ($match === null) {
            $message = sprintf('Date was not resolved correctly from "%s" file', $filePath);
            throw new ShouldNotHappenException($message);
        }

        $year = (int) $match['year'];
        $month = (int) $match['month'];
        $day = (int) $match['day'];

        $date = sprintf('%d-%d-%d', $year, $month, $day);
        return DateTime::from($date);
    }

    public function getSlug(string $filePath): string
    {
        $dateTime = $this->resolveDateTime($filePath);
        $dateAndNamePattern = sprintf('#%s-%s#', self::DATE_REGEX, self::NAME_REGEX);

        $match = (array) Strings::match($filePath, $dateAndNamePattern);

        $dateLessBreakDateTime = DateTime::from('2021-02-22');
        if ($dateTime >= $dateLessBreakDateTime) {
            return $match['name'];
        }

        return $dateTime->format('Y/m/d') . '/' . $match['name'];
    }
}
