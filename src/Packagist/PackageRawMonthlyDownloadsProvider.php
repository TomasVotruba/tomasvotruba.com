<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Packagist;

use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\Json\FileToJsonLoader;

final class PackageRawMonthlyDownloadsProvider
{
    /**
     * @var string
     */
    private const URL_DOWNLOAD_STATS = 'https://packagist.org/packages/%s/stats/all.json?average=monthly';

    /**
     * @var FileToJsonLoader
     */
    private $fileToJsonLoader;

    public function __construct(FileToJsonLoader $fileToJsonLoader)
    {
        $this->fileToJsonLoader = $fileToJsonLoader;
    }

    /**
     * @return int[]
     */
    public function provideForPackage(string $packageName): array
    {
        $url = sprintf(self::URL_DOWNLOAD_STATS, $packageName);
        $json = $this->fileToJsonLoader->load($url);

        if (! isset($json['values'])) {
            throw new ShouldNotHappenException();
        }

        $values = $json['values'];

        // last value is uncompleted month, not needed
        // array_pop($values);

        // put the highest first to keep convention
        return array_reverse($values);
    }
}
