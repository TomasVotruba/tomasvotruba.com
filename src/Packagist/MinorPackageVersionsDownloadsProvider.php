<?php declare(strict_types=1);

namespace TomasVotruba\Website\Packagist;

use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\Json\FileToJsonLoader;
use TomasVotruba\Website\VersionManipulator;

final class MinorPackageVersionsDownloadsProvider
{
    /**
     * @var string
     */
    public const DOWNLOADS_MINOR = 'downloads_minor';

    /**
     * @var string
     */
    private const URL_PACKAGE_STATS = 'https://packagist.org/packages/%s/downloads.json';

    /**
     * @var FileToJsonLoader
     */
    private $fileToJsonLoader;

    /**
     * @var VersionManipulator
     */
    private $versionManipulator;

    public function __construct(FileToJsonLoader $fileToJsonLoader, VersionManipulator $versionManipulator)
    {
        $this->fileToJsonLoader = $fileToJsonLoader;
        $this->versionManipulator = $versionManipulator;
    }

    /**
     * @return int[]
     */
    public function provideForPackage(string $packageName): array
    {
        $data = $this->getDownloadsByVersionForPackage($packageName);
        $data = $this->sortByVersion($data);

        $downloadsGroupedByMajorAndMinorVersion = [];

        foreach ($data as $version => $downloads) {
            $version = $this->versionManipulator->create($version);
            $minorVersion = $this->versionManipulator->resolveToMinor($version);

            $monthlyDownloads = $downloads['monthly'];

            // too small to notice
            if ($monthlyDownloads < 500) {
                continue;
            }

            if (isset($downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MINOR][$minorVersion])) {
                $downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MINOR][$minorVersion] += $monthlyDownloads;
            } else {
                $downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MINOR][$minorVersion] = $monthlyDownloads;
            }
        }

        // skip single minor versions, no added value
        if ($this->hasLessThanTwoMinorVersions($downloadsGroupedByMajorAndMinorVersion)) {
            return [];
        }

        /** @var int[] $downloadsGroupedByMajorAndMinorVersion */
        return $downloadsGroupedByMajorAndMinorVersion;
    }

    /**
     * @return int[][]
     */
    private function getDownloadsByVersionForPackage(string $packageName): array
    {
        $url = sprintf(self::URL_PACKAGE_STATS, $packageName);
        $json = $this->fileToJsonLoader->load($url);

        if (! isset($json['package']['downloads']['versions'])) {
            throw new ShouldNotHappenException();
        }

        $data = $json['package']['downloads']['versions'];

        return array_filter($data, function (string $version): bool {
            return $this->versionManipulator->isValid($version);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function sortByVersion(array $data): array
    {
        uksort($data, function ($firstVersion, $secondVersion) {
            $firstVersion = $this->versionManipulator->create($firstVersion);
            $secondVersion = $this->versionManipulator->create($secondVersion);

            return $secondVersion->isGreaterThan($firstVersion);
        });

        return $data;
    }

    private function hasLessThanTwoMinorVersions(array $downloadsGroupedByMajorAndMinorVersion): bool
    {
        if (! isset($downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MINOR])) {
            return true;
        }

        return count($downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MINOR]) < 2;
    }
}
