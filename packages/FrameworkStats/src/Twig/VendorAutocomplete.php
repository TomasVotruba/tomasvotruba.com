<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Twig;

final class VendorAutocomplete
{
    /**
     * @var string
     */
    public $vendor_name;

    /**
     * @var float
     */
    public $last_year_trend;

    /**
     * @var int
     */
    public $vendor_total_last_year;

    /**
     * @var int
     */
    public $vendor_total_previous_year;

    /**
     * @var PackageAutocomplete[]
     */
    public $packages_data = [];
}
