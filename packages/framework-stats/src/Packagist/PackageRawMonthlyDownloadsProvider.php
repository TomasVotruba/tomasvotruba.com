<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Packagist;

use Nette\Utils\DateTime;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;
use TomasVotruba\FrameworkStats\Json\FileToJsonLoader;

final class PackageRawMonthlyDownloadsProvider
{
    /**
     * @var string
     */
    private const URL_DOWNLOAD_STATS = 'https://packagist.org/packages/%s/stats/all.json?average=monthly';

    /**
     * @var string
     */
    private const VALUES = 'values';

    public function __construct(
        private FileToJsonLoader $fileToJsonLoader
    ) {
    }

    /**
     * @return int[]
     */
    public function provideForPackage(string $packageName): array
    {
        $url = sprintf(self::URL_DOWNLOAD_STATS, $packageName);
        $json = $this->fileToJsonLoader->load($url);

        if (! isset($json[self::VALUES])) {
            throw new ShouldNotHappenException();
        }

        // some package don't have stats yet
        if (! isset($json[self::VALUES][$packageName])) {
            return [];
        }

        $values = array_combine($json['labels'], $json[self::VALUES][$packageName]);
        if ($values === []) {
            throw new ShouldNotHappenException();
        }

        $valuesSortedByNewest = array_reverse($values, true);

        // drop current is uncompleted month and can return different values for inter-dependent packages, not needed
        $firstKey = array_key_first($valuesSortedByNewest);

        $nowDateTime = new DateTime();
        $currentMonth = $nowDateTime->format('Y-m');
        if ($currentMonth === $firstKey) {
            array_shift($valuesSortedByNewest);
        }

        // put the highest first to keep convention
        return $valuesSortedByNewest;
    }
}
