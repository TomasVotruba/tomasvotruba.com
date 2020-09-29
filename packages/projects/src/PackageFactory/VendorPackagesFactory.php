<?php

declare(strict_types=1);

namespace TomasVotruba\Projects\PackageFactory;

use Spatie\Packagist\PackagistClient;
use TomasVotruba\Projects\ValueObject\PackagistPackage;
use TomasVotruba\Tweeter\Exception\ShouldNotHappenException;

final class VendorPackagesFactory
{
    private PackagistClient $packagistClient;

    public function __construct(PackagistClient $packagistClient)
    {
        $this->packagistClient = $packagistClient;
    }

    /**
     * @return PackagistPackage[]
     */
    public function createPackagesByVendor(string $vendor): array
    {
        $packagistPackages = [];

        $symplifyPackages = $this->packagistClient->getPackagesNamesByVendor($vendor);
        if ($symplifyPackages === null) {
            throw new ShouldNotHappenException();
        }

        foreach ($symplifyPackages['packageNames'] as $symplifyPackageName) {
            $packageMetadata = $this->packagistClient->getPackageMetadata($symplifyPackageName);
            if ($packageMetadata === null) {
                throw new ShouldNotHappenException();
            }

            $packageMetadata = $packageMetadata['packages'][$symplifyPackageName];
            $packageMetadata = array_pop($packageMetadata);

            $packagistPackages[] = new PackagistPackage($packageMetadata['name'], $packageMetadata['description']);

            // @todo stats
        }

        return $packagistPackages;
    }
}
