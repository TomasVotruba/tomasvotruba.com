<?php declare(strict_types=1);

namespace TomasVotruba\Website\Packagist;

use Nette\Utils\Strings;
use PharIo\Version\InvalidVersionException;
use PharIo\Version\Version;
use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\Json\FileToJsonLoader;

final class MinorPackageVersionsDownloadsProvider
{
    /**
     * @var string
     */
    private const URL_PACKAGE_STATS = 'https://packagist.org/packages/%s/downloads.json';

    /**
     * @var string
     */
    private const DOWNLOADS_MINOR = 'downloads_minor';

    /**
     * @var string
     */
    private const DOWNLOADS_MAJOR = 'downloads_major';

    /**
     * @var Version[]
     */
    private $cachedVersionObjects = [];

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
        $data = $this->getDownloadsByVersionForPackage($packageName);
        $data = $this->filterOutInvalidVersions($data);
        $data = $this->sortByVersion($data);

        $downloadsGroupedByMajorAndMinorVersion = [];

        foreach ($data as $version => $downloads) {
            $version = $this->createVersionObject($version);

            $minorVersion = 'v' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();
            $majorVersion = 'v' . $version->getMajor()->getValue();

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

            if (isset($downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MAJOR][$majorVersion])) {
                $downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MAJOR][$majorVersion] += $monthlyDownloads;
            } else {
                $downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MAJOR][$majorVersion] = $monthlyDownloads;
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

        // remove all dev versions
        foreach ($data as $key => $downloads) {
            if (Strings::match($key, '#^dev\-#')) {
                unset($data[$key]);
            }

            if (Strings::match($key, '#(alpha|beta|rc)#i')) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    private function filterOutInvalidVersions(array $data): array
    {
        /** @var string $version */
        foreach ($data as $version => $downloads) {
            $key = $version;

            try {
                /** @var Version $version */
                $version = new Version($version);
            } catch (InvalidVersionException $invalidVersionException) {
                // invalid version
                unset($data[$key]);
            }
        }

        return $data;
    }

    private function sortByVersion(array $data): array
    {
        uksort($data, function ($firstVersion, $secondVersion) {
            $firstVersion = $this->createVersionObject($firstVersion);
            $secondVersion = $this->createVersionObject($secondVersion);

            return $secondVersion->isGreaterThan($firstVersion);
        });

        return $data;
    }

    private function createVersionObject(string $version): Version
    {
        if (isset($this->cachedVersionObjects[$version])) {
            return $this->cachedVersionObjects[$version];
        }
        $this->cachedVersionObjects[$version] = new Version($version);

        return $this->cachedVersionObjects[$version];
    }

    private function hasLessThanTwoMinorVersions(array $downloadsGroupedByMajorAndMinorVersion): bool
    {
        if (! isset($downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MINOR])) {
            return true;
        }

        return count($downloadsGroupedByMajorAndMinorVersion[self::DOWNLOADS_MINOR]) < 2;
    }
}
