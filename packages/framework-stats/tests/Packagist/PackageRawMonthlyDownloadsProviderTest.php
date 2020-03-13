<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Tests\Packagist;

use Nette\Utils\DateTime;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use TomasVotruba\FrameworkStats\Packagist\PackageRawMonthlyDownloadsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class PackageRawMonthlyDownloadsProviderTest extends AbstractKernelTestCase
{
    /**
     * @var PackageRawMonthlyDownloadsProvider
     */
    private $packageRawMonthlyDownloadsProvider;

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

        $this->assertGreaterThan(10, count($symplifyPackageBuilderStats));
        $monthValue = array_key_first($symplifyPackageBuilderStats);

        $previousMonth = (new DateTime('-1 month'))->format('Y-m');

        $this->assertSame($previousMonth, $monthValue);
    }
}
