<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Packagist;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;
use TomasVotruba\FrameworkStats\Json\FileToJsonLoader;
use TomasVotruba\Website\ValueObject\Option as OptionAlias;

final class VendorPackagesProvider
{
    /**
     * @var string
     */
    private const URL_VENDOR_PACKAGES = 'https://packagist.org/packages/list.json?vendor=%s';

    private FileToJsonLoader $fileToJsonLoader;

    /**
     * @var string[]
     */
    private array $excludedFrameworkPackages = [];

    public function __construct(FileToJsonLoader $fileToJsonLoader, ParameterProvider $parameterProvider)
    {
        $this->fileToJsonLoader = $fileToJsonLoader;
        $this->excludedFrameworkPackages = $parameterProvider->provideArrayParameter(
            OptionAlias::EXCLUDED_FRAMEWORK_PACKAGES
        );
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

        return $this->excludeUndesiredPackages($packageNames);
    }

    /**
     * @param string[] $packageNames
     * @return string[]
     */
    private function excludeUndesiredPackages(array $packageNames): array
    {
        foreach ($packageNames as $key => $packageName) {
            if (! $this->isPackageExcluded($packageName)) {
                continue;
            }

            unset($packageNames[$key]);
        }
        return $packageNames;
    }

    private function isPackageExcluded(string $packageName): bool
    {
        foreach ($this->excludedFrameworkPackages as $excludedFrameworkPackage) {
            if (Strings::contains($excludedFrameworkPackage, '*') && fnmatch($excludedFrameworkPackage, $packageName)) {
                return true;
            }

            if ($packageName === $excludedFrameworkPackage) {
                return true;
            }
        }

        return false;
    }
}
