<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Tests;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use TomasVotruba\FrameworkStats\Statistics;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class StatisticsTest extends AbstractKernelTestCase
{
    /**
     * @var array<string, int>
     */
    private const AVERAGE_DAILY_VALUES_BY_MONTH = [
        '2019-12' => 300,
    ];

    private Statistics $statistics;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->statistics = self::$container->get(Statistics::class);
    }

    public function test(): void
    {
        $monthlyValuesByMonth = $this->statistics->expandDailyAverageToMonthTotal(self::AVERAGE_DAILY_VALUES_BY_MONTH);
        $this->assertSame([
            '2019-12' => 9_300,
        ], $monthlyValuesByMonth);
    }
}
