<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\FileSystem;

use DateTimeInterface;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;

final class PathAnalyzer
{
    /**
     * @var string
     */
    private const DATE_PATTERN = '(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})';

    /**
     * @var string
     */
    private const NAME_PATTERN = '(?<name>[\w\d-]*)';

    public function detectDate(SmartFileInfo $fileInfo): ?DateTimeInterface
    {
        $match = Strings::match($fileInfo->getFilename(), '#' . self::DATE_PATTERN . '#');
        if ($match === null) {
            return null;
        }

        $date = sprintf('%d-%d-%d', $match['year'], $match['month'], $match['day']);

        return DateTime::from($date);
    }

    public function getSlug(SmartFileInfo $fileInfo): string
    {
        $date = $this->detectDate($fileInfo);

        if ($date === null) {
            throw new ShouldNotHappenException();
        }

        $dateAndNamePattern = sprintf('#%s-%s#', self::DATE_PATTERN, self::NAME_PATTERN);

        $match = (array) Strings::match($fileInfo->getFilename(), $dateAndNamePattern);

        return $date->format('Y/m/d') . '/' . $match['name'];
    }
}
