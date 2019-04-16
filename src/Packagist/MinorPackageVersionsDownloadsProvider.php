<?php declare(strict_types=1);

namespace TomasVotruba\Website\Packagist;

use Nette\Utils\Strings;
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

            $version = new Version($version);

            $minorVersion = 'v' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();

            if (isset($downloadsGroupedByVersion[$minorVersion])) {
                $downloadsGroupedByVersion[$minorVersion] += $downloads['monthly'];
            } else {
                $downloadsGroupedByVersion[$minorVersion] = $downloads['monthly'];
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

        unset($data['dev-master']); // could be any version

        return $data;
    }
}
