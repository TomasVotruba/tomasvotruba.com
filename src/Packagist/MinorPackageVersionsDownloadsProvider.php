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

        $downloadsGroupedByVersion = [];

        // group by minor version
        /** @var string $version */
        foreach ($data as $version => $downloads) {
            // clear versoin to semver
            if (Strings::endsWith($version, '.x-dev')) {
                /** @var string $version */
                $version = Strings::before($version, '.x-dev');
            }

            try {
                /** @var Version $version */
                $version = new Version($version);
            } catch (InvalidVersionException $invalidVersionException) {
                // invalid version
                continue;
            }

            $minorVersion = 'v' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();

            $monthlyDownloads = $downloads['monthly'];

            // too small to notice
            if ($monthlyDownloads < 1000) {
                continue;
            }

            if (isset($downloadsGroupedByVersion[$minorVersion])) {
                $downloadsGroupedByVersion[$minorVersion] += $monthlyDownloads;
            } else {
                $downloadsGroupedByVersion[$minorVersion] = $monthlyDownloads;
            }
        }

        krsort($downloadsGroupedByVersion);

        /** @var int[] $downloadsGroupedByVersion */
        return $downloadsGroupedByVersion;
    }

    /**
     * @return int[]
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
}
