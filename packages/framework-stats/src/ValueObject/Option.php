<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\ValueObject;

final class Option
{
    /**
     * @api
     * @var string
     */
    public const FRAMEWORKS_VENDOR_TO_NAME = 'frameworks_vendor_to_name';

    /**
     * @api
     * @var string
     */
    public const EXCLUDED_FRAMEWORK_PACKAGES = 'excluded_framework_packages';
}
