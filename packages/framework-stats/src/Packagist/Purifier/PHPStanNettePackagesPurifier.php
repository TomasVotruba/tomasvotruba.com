<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Packagist\Purifier;

/**
 * Thanks Jan Kuchar for tip on this intervention that was hard to spot (= invisible) for me.
 */
final class PHPStanNettePackagesPurifier
{
    /**
     * List packages used by PHPStan up to version 0.11.* included
     * @var string[]
     */
    private const INTERVENING_DEPENDENCIES = [
        // https://packagist.org/packages/phpstan/phpstan#0.11.19
        'nette/bootstrap',
        'nette/di',
        'nette/neon',
        'nette/robot-loader',
        'nette/schema',
        'nette/utils',
        // eventually
        'nette/php-generator',
        'nette/finder',
    ];

    public function correctLastYearDownloads(int $yearDownloads, string $packageName): int
    {
        if (! in_array($packageName, self::INTERVENING_DEPENDENCIES, true)) {
            return $yearDownloads;
        }

        // packagist doesn't provide way to count this number, so it is estimation compared to another nette packages,
        // and phpstan downloads from https://packagist.org/packages/phpstan/phpstan/stats
        $phpstanLastYearInfluence = 5_500_000;

        if ($yearDownloads > $phpstanLastYearInfluence) {
            $yearDownloads -= $phpstanLastYearInfluence;
        }

        return $yearDownloads;
    }

    public function correctPreviousYearDownloads(int $yearDownloads, string $packageName): int
    {
        if (! in_array($packageName, self::INTERVENING_DEPENDENCIES, true)) {
            return $yearDownloads;
        }

        // packagist doesn't provide way to count this number, so it is estimation compared to another nette packages,
        // and phpstan downloads from https://packagist.org/packages/phpstan/phpstan/stats
        $phpstanLastYearInfluence = 1_900_000;

        if ($yearDownloads > $phpstanLastYearInfluence) {
            $yearDownloads -= $phpstanLastYearInfluence;
        }

        return $yearDownloads;
    }
}
