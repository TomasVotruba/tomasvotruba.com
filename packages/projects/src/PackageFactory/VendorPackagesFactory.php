<?php

declare(strict_types=1);

namespace TomasVotruba\Projects\PackageFactory;

use Spatie\Packagist\PackagistClient;
use TomasVotruba\Projects\ValueObject\Package;
use TomasVotruba\Tweeter\Exception\ShouldNotHappenException;

/**
 * @see https://github.com/spatie/packagist-api
 */
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
        $packages = $this->createPackages($vendor);

        usort(
            $packages,
            fn (Package $firstPackage, Package $secondPackage) => $secondPackage->getTotalDownloads() <=> $firstPackage->getTotalDownloads()
        );

        return $packages;
    }

    private function getPackageData(string $packageName): array
    {
        $packageData = $this->packagistClient->getPackage($packageName);
        if ($packageData === null) {
            throw new ShouldNotHappenException();
        }

        return $packageData['package'];
    }

    private function createPackages(string $vendor): array
    {
        $packages = [];

        $symplifyPackages = $this->packagistClient->getPackagesNamesByVendor($vendor);
        if ($symplifyPackages === null) {
            throw new ShouldNotHappenException();
        }

        foreach ($symplifyPackages['packageNames'] as $symplifyPackageName) {
            $packageData = $this->getPackageData($symplifyPackageName);

            // skip
            if (isset($packageData['abandoned'])) {
                continue;
            }

            $packages[] = new Package(
                $packageData['name'],
                $packageData['description'],
                $packageData['repository'],
                $packageData['github_stars'],
                $packageData['downloads']['total']
            );
        }

        return $packages;
    }
}
