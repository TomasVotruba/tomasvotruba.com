<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Packagist\Purifier;

use TomasVotruba\FrameworkStats\Packagist\PackageRawMonthlyDownloadsProvider;

final class InterveningPackagesPurifier
{
    /**
     * @var string[][]
     */
    private const INTERVENING_DEPENDENCIES = [
        // https://packagist.org/packages/friendsofphp/php-cs-fixer
        'friendsofphp/php-cs-fixer' => [
            'symfony/console',
            'symfony/event-dispatcher',
            'symfony/filesystem',
            'symfony/finder',
            'symfony/options-resolver',
            'symfony/polyfill-php70',
            'symfony/polyfill-php72',
            'symfony/process',
            'symfony/stopwatch',
            // consequently
            'symfony/contracts',
            'symfony/polyfill-mbstring',
            'symfony/polyfill-ctype',
        ],
        // https://packagist.org/packages/robmorgan/phinx
        'robmorgan/phinx' => [
            'symfony/console',
            'symfony/config',
            'symfony/yaml',
            // consequently
            'symfony/contracts',
            'symfony/polyfill-mbstring',
            'symfony/filesystem',
            'symfony/polyfill-ctype',
        ],
        // https://packagist.org/packages/laravel/framework
        'laravel/framework' => [
            'symfony/console',
            'symfony/debug',
            'symfony/finder',
            'symfony/http-foundation',
            'symfony/http-kernel',
            'symfony/process',
            'symfony/routing',
            'symfony/var-dumper',
            // consequently
            'symfony/contracts',
            'symfony/polyfill-mbstring',
            'symfony/contracts',
            'symfony/event-dispatcher',
            'symfony/http-foundation',
            'symfony/debug',
            'symfony/polyfill-ctype',
            'symfony/polyfill-php72',
        ],
        // particular laravel to symfony deps
        'illuminate/queue' => ['symfony/debug', 'symfony/process'],
        'illuminate/http' => ['symfony/http-foundation', 'symfony/http-kernel'],
        'illuminate/validation' => ['symfony/http-foundation'],
        'illuminate/session' => ['symfony/finder', 'symfony/http-foundation'],
        'illuminate/console' => ['symfony/console', 'symfony/process'],
        'illuminate/view' => ['symfony/debug'],
        'illuminate/filesystem' => ['symfony/finder'],
        'illuminate/routing' => ['symfony/debug', 'symfony/http-foundation', 'symfony/http-kernel', 'symfony/routing'],
    ];

    /**
     * @var mixed[][]
     */
    private array $interveningPackagesDownloads = [];

    public function __construct(
        private PackageRawMonthlyDownloadsProvider $packageRawMonthlyDownloadsProvider
    ) {
    }

    public function correctInterveningPackages(array $monthlyDownloads, string $packageName): array
    {
        foreach (self::INTERVENING_DEPENDENCIES as $interveningDependency => $dependingPackages) {
            if (! in_array($packageName, $dependingPackages, true)) {
                continue;
            }

            $interveningDownloads = $this->getInterveningPackageDownloads($interveningDependency);
            foreach (array_keys($monthlyDownloads) as $key) {
                // too old
                if (! isset($interveningDownloads[$key])) {
                    break;
                }

                // correction here!
                $monthlyDownloads[$key] -= $interveningDownloads[$key];
            }
        }

        return $monthlyDownloads;
    }

    private function getInterveningPackageDownloads(string $packageName): array
    {
        if (isset($this->interveningPackagesDownloads[$packageName])) {
            return $this->interveningPackagesDownloads[$packageName];
        }

        $interveningRawMonthlyDownloads = $this->packageRawMonthlyDownloadsProvider->provideForPackage($packageName);

        $this->interveningPackagesDownloads[$packageName] = $interveningRawMonthlyDownloads;

        return $this->interveningPackagesDownloads[$packageName];
    }
}
