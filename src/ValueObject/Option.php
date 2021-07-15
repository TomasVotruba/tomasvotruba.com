<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const SITE_URL = 'site_url';

    /**
     * @var string
     */
    public const KERNEL_PROJECT_DIR = 'kernel.project_dir';

    /**
     * @var string
     */
    public const TWITTER_MAXIMAL_DAYS_IN_PAST = 'twitter_maximal_days_in_past';

    /**
     * @var string
     */
    public const TWITTER_OAUTH_ACCESS_TOKEN_SECRET = 'twitter_oauth_access_token_secret';

    /**
     * @var string
     */
    public const TWITTER_NAME = 'twitter_name';

    /**
     * @var string
     */
    public const TWITTER_CONSUMER_SECRET = 'twitter_consumer_secret';

    /**
     * @var string
     */
    public const TWITTER_CONSUMER_KEY = 'twitter_consumer_key';

    /**
     * @var string
     */
    public const TWITTER_OAUTH_ACCESS_TOKEN = 'twitter_oauth_access_token';

    /**
     * @var string
     */
    public const TWITTER_MINIMAL_GAP_IN_HOURS = 'twitter_minimal_gap_in_hours';

    /**
     * @var string
     */
    public const DRY_RUN = 'dry-run';
}
