<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Twig;

final class VendorAutocomplete
{
    public string $vendor_name;

    public float $last_year_trend;

    public int $vendor_total_last_year;

    public int $vendor_total_previous_year;

    /**
     * @var PackageAutocomplete[]
     */
    public array $packages_data = [];
}
