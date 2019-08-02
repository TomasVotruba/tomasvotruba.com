<?php declare(strict_types=1);

namespace TomasVotruba\Website\Packagist;

use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\Json\FileToJsonLoader;

final class VendorPackagesProvider
{
    /**
     * @var string
     */
    private const URL_VENDOR_PACKAGES = 'https://packagist.org/packages/list.json?vendor=%s';

    /**
     * @var FileToJsonLoader
     */
    private $fileToJsonLoader;

    /**
     * @var string[]
     */
    private $excludedFrameworkPackages = [];

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
        $packageNames = array_diff($packageNames, $this->excludedFrameworkPackages);

        return $packageNames;
    }
}
