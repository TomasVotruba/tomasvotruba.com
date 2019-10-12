<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Packagist;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\Json\FileToJsonLoader;
use TomasVotruba\Website\VersionManipulator;

final class PackageVersionPublishDatesProvider
{
    /**
     * @var string
     */
    private const URL_PACKAGE_DETAIL = 'https://repo.packagist.org/p/%s.json';

    /**
     * @var mixed[]
     */
    private $dataByPackageName = [];

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

    public function provideForPackageAndVersion(string $packageName, string $version): ?string
    {
        return $this->provideForPackage($packageName)[$version] ?? null;
    }

    /**
     * @return string[]
     */
    public function provideForPackage(string $packageName): array
    {
        if (isset($this->dataByPackageName[$packageName])) {
            return $this->dataByPackageName[$packageName];
        }

        $data = $this->getDataByVersionForPackage($packageName);

        $versionToDate = [];
        foreach ($data as $version => $versionData) {
            if (! $this->versionManipulator->isValid((string) $version)) {
                continue;
            }

            // skip non-first version
            if (! Strings::match($version, '#\.0$#')) {
                continue;
            }

            $version = $this->versionManipulator->create($version);
            $minorVersion = $this->versionManipulator->resolveToMinor($version);

            $versionToDate[$minorVersion] = DateTime::from($versionData['time'])->format('Y-m-d');
        }

        $this->dataByPackageName[$packageName] = $versionToDate;

        return $versionToDate;
    }

    /**
     * @return int[][]
     */
    private function getDataByVersionForPackage(string $packageName): array
    {
        $url = sprintf(self::URL_PACKAGE_DETAIL, $packageName);
        $json = $this->fileToJsonLoader->load($url);

        if (! isset($json['packages'][$packageName])) {
            throw new ShouldNotHappenException();
        }

        return $json['packages'][$packageName];
    }
}
