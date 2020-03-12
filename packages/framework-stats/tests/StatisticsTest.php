<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Tests;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use TomasVotruba\FrameworkStats\Statistics;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class StatisticsTest extends AbstractKernelTestCase
{
    /**
     * @var Statistics
     */
    private $statistics;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->statistics = self::$container->get(Statistics::class);
    }

    public function test(): void
    {
        $averageDailyValuesByMonth = ['2019-12' => 300];

        $monthlyValuesByMonth = $this->statistics->expandDailyAverageToMonthTotal($averageDailyValuesByMonth);

        $this->assertSame(['2019-12' => 9300], $monthlyValuesByMonth);
    }
}
