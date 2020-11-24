<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Tests\Packagist;

use Nette\Utils\DateTime;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use TomasVotruba\FrameworkStats\Packagist\PackageRawMonthlyDownloadsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class PackageRawMonthlyDownloadsProviderTest extends AbstractKernelTestCase
{
    private PackageRawMonthlyDownloadsProvider $packageRawMonthlyDownloadsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->packageRawMonthlyDownloadsProvider = self::$container->get(PackageRawMonthlyDownloadsProvider::class);
    }

    public function test(): void
    {
        $symplifyPackageBuilderStats = $this->packageRawMonthlyDownloadsProvider->provideForPackage(
            'symplify/package-builder'
        );

        $statsCount = count($symplifyPackageBuilderStats);
        $this->assertGreaterThan(10, $statsCount);

        $previousMonth = DateTime::from('- 2 months')->format('Y-m');

        $this->assertArrayHasKey($previousMonth, $symplifyPackageBuilderStats);
    }
}
