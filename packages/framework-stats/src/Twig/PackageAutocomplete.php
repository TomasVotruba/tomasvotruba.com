<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Twig;

final class PackageAutocomplete
{
    public string $package_short_name;

    public string $package_name;

    public float $last_year_trend;

    public int $last_12_months;

    public int $previous_12_months;
}
