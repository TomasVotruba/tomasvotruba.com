<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Contract;

interface LastYearTrendAwareInterface
{
    public function getLastYearTrend(): float;
}
