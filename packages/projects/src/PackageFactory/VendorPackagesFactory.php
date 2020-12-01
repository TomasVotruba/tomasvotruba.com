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
    public function __construct(private PackagistClient $packagistClient)
    {
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

    /**
     * @param string[] $packageNames
     * @return Package[]
     */
    public function createPackagesByPackageNames(array $packageNames): array
    {
        return $this->createPackagesFromPackagesNames($packageNames);
    }

    private function getPackageData(string $packageName): array
    {
        $packageData = $this->packagistClient->getPackage($packageName);
        if ($packageData === null) {
            throw new ShouldNotHappenException();
        }

        return $packageData['package'];
    }

    /**
     * @return Package[]
     */
    private function createPackages(string $vendor): array
    {
        $packageNames = $this->resolvePackageNamesByVendorName($vendor);
        return $this->createPackagesFromPackagesNames($packageNames);
    }

    private function resolvePackageNamesByVendorName(string $vendor): array
    {
        $vendorPackages = $this->packagistClient->getPackagesNamesByVendor($vendor);
        if ($vendorPackages === null) {
            throw new ShouldNotHappenException();
        }

        return $vendorPackages['packageNames'];
    }

    /**
     * @param string[] $packageNames
     * @return Package[]
     */
    private function createPackagesFromPackagesNames(array $packageNames): array
    {
        $packages = [];

        foreach ($packageNames as $packageName) {
            $packageData = $this->getPackageData($packageName);

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
