<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObject;

final class Option
{
    /**
     * @api
     * @var string
     */
    public const SITE_URL = 'site_url';

    /**
     * @api
     * @var string
     */
    public const KERNEL_PROJECT_DIR = 'kernel.project_dir';

    /**
     * @api
     * @var string
     */
    public const TWITTER_NAME = 'twitter_name';

    /**
     * @api
     * @var string
     */
    public const TWITTER_MINIMAL_GAP_IN_DAYS = 'twitter_minimal_gap_in_days';

    /**
     * @api
     * @var string
     */
    public const TWITTER_MAXIMAL_DAYS_IN_PAST = 'twitter_maximal_days_in_past';

    /**
     * @api
     * @var string
     */
    public const TWITTER_CONSUMER_KEY = 'twitter_consumer_key';

    /**
     * @api
     * @var string
     */
    public const TWITTER_CONSUMER_SECRET = 'twitter_consumer_secret';

    /**
     * @api
     * @var string
     */
    public const TWITTER_OAUTH_ACCESS_TOKEN = 'twitter_oauth_access_token';

    /**
     * @api
     * @var string
     */
    public const TWITTER_OAUTH_ACCESS_TOKEN_SECRET = 'twitter_oauth_access_token_secret';
}
