<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Twig;

final class PackageAutocomplete
{
    public string $packageShortName;

    public string $packageName;

    public float $lastYearTrend;

    public int $last12Months;

    public int $previous12Months;
}
