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

        $downloadsGroupedByVersionAndMajorMinor = [];

        $data = $this->filterOutInvalidVersions($data);

        /** @var string $version */
        foreach ($data as $version => $downloads) {
            /** @var Version $version */
            $version = new Version($version);

            $minorVersion = 'v' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();
            $majorVersion = 'v' . $version->getMajor()->getValue();

            $monthlyDownloads = $downloads['monthly'];

            // too small to notice
            if ($monthlyDownloads < 500) {
                continue;
            }

            if (isset($downloadsGroupedByVersionAndMajorMinor['downloads_minor'][$minorVersion])) {
                $downloadsGroupedByVersionAndMajorMinor['downloads_minor'][$minorVersion] += $monthlyDownloads;
            } else {
                $downloadsGroupedByVersionAndMajorMinor['downloads_minor'][$minorVersion] = $monthlyDownloads;
            }

            if (isset($downloadsGroupedByVersionAndMajorMinor['downloads_major'][$majorVersion])) {
                $downloadsGroupedByVersionAndMajorMinor['downloads_major'][$majorVersion] += $monthlyDownloads;
            } else {
                $downloadsGroupedByVersionAndMajorMinor['downloads_major'][$majorVersion] = $monthlyDownloads;
            }

            $downloadsGroupedByVersionAndMajorMinor = $this->sortByVersion($downloadsGroupedByVersionAndMajorMinor);
        }

        /** @var int[] $downloadsGroupedByVersionAndMajorMinor */
        return $downloadsGroupedByVersionAndMajorMinor;
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

    private function sortByVersion(array $downloadsGroupedByVersionAndMajorMinor): array
    {
        uksort($downloadsGroupedByVersionAndMajorMinor['downloads_minor'], function ($firstVersion, $secondVersion) {
            $firstVersion = new Version($firstVersion);
            $secondVersion = new Version($secondVersion);

            return $firstVersion->isGreaterThan($secondVersion);
        });

        uksort($downloadsGroupedByVersionAndMajorMinor['downloads_major'], function ($firstVersion, $secondVersion) {
            $firstVersion = new Version($firstVersion . '.0');
            $secondVersion = new Version($secondVersion . '.0');

            return $firstVersion->isGreaterThan($secondVersion);
        });

        return $downloadsGroupedByVersionAndMajorMinor;
    }
}
