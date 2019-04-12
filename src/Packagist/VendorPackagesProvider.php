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

    public function __construct(FileToJsonLoader $fileToJsonLoader)
    {
        $this->fileToJsonLoader = $fileToJsonLoader;
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

        return $packageNames;
    }
}
