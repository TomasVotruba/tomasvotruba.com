<?php

declare(strict_types=1);

namespace TomasVotruba\Projects\PackageFactory;

use Spatie\Packagist\PackagistClient;
use TomasVotruba\Projects\ValueObject\Package;
use TomasVotruba\Tweeter\Exception\ShouldNotHappenException;

final class VendorPackagesFactory
{
    private PackagistClient $packagistClient;

    public function __construct(PackagistClient $packagistClient)
    {
        $this->packagistClient = $packagistClient;
    }

    /**
     * @return Package[]
     */
    public function createPackagesByVendor(string $vendor): array
    {
        $packagistPackages = [];

        $symplifyPackages = $this->packagistClient->getPackagesNamesByVendor($vendor);
        if ($symplifyPackages === null) {
            throw new ShouldNotHappenException();
        }

        foreach ($symplifyPackages['packageNames'] as $symplifyPackageName) {
            $packageMetadata = $this->getPackageMetadata($symplifyPackageName);

            // skip
            if (isset($packageMetadata['abandoned'])) {
                continue;
            }

            $packagistPackages[] = new Package(
                $packageMetadata['name'],
                $packageMetadata['description'],
                $packageMetadata['source']['url']
            );
        }

        return $packagistPackages;
    }

    private function getPackageMetadata(string $packageName): array
    {
        $packageMetadata = $this->packagistClient->getPackageMetadata($packageName);
        if ($packageMetadata === null) {
            throw new ShouldNotHappenException();
        }

        $packageMetadata = $packageMetadata['packages'][$packageName];
        return array_pop($packageMetadata);
    }
}
