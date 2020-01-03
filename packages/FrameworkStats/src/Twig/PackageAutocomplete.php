<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Twig;

final class PackageAutocomplete
{
    /**
     * @var string
     */
    public $package_short_name;

    /**
     * @var string
     */
    public $package_name;

    /**
     * @var float
     */
    public $last_year_trend;

    /**
     * @var int
     */
    public $last_12_months;

    /**
     * @var int
     */
    public $previous_12_months;
}
