<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Contract\FrameworkStats;

interface LastYearTrendAwareInterface
{
    public function getLastYearTrend(): float;
}
