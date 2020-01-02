<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Packagist;

use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;
use TomasVotruba\FrameworkStats\Json\FileToJsonLoader;

final class VendorPackagesProvider
{
    /**
     * @var string
     */
    private const URL_VENDOR_PACKAGES = 'https://packagist.org/packages/list.json?vendor=%s';

    /**
     * @var string[]
     */
    private $excludedFrameworkPackages = [];

    /**
     * @var FileToJsonLoader
     */
    private $fileToJsonLoader;

    /**
     * @param string[] $excludedFrameworkPackages
     */
    public function __construct(FileToJsonLoader $fileToJsonLoader, array $excludedFrameworkPackages)
    {
        $this->fileToJsonLoader = $fileToJsonLoader;
        $this->excludedFrameworkPackages = $excludedFrameworkPackages;
    }

    /**
     * @return string[]
     */
    public function provideForVendor(string $vendorName): array
    {
        $url = sprintf(self::URL_VENDOR_PACKAGES, $vendorName);

        $json = $this->fileToJsonLoader->load($url);

        if (! isset($json['packageNames'])) {
            throw new ShouldNotHappenException();
        }

        $packageNames = $json['packageNames'];

        // include laravel/framework monorepo
        if ($vendorName === 'illuminate') {
            $packageNames[] = 'laravel/framework';
        }

        // exclude undesired packages
        return array_diff($packageNames, $this->excludedFrameworkPackages);
    }
}
