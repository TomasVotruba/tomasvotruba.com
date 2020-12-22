<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const HELPED_COMPANIES = 'helped_companies';

    /**
     * @var string
     */
    public const REFERENCES = 'references';

    /**
     * @var string
     */
    public const CONTRIBUTORS = 'contributors';

    /**
     * @var string
     */
    public const SITE_URL = 'site_url';

    /**
     * @api
     * @var string
     */
    public const FRAMEWORKS_VENDOR_TO_NAME = 'frameworks_vendor_to_name';

    /**
     * @var string
     */
    public const PHP_FRAMEWORK_TRENDS = 'php_framework_trends';

    /**
     * @api
     * @var string
     */
    public const EXCLUDED_FRAMEWORK_PACKAGES = 'excluded_framework_packages';

    /**
     * @var string
     * @api
     */
    public const THANKER_REPOSITORY_NAME = 'thanker_repository_name';

    /**
     * @var string
     * @api
     */
    public const GITHUB_TOKEN = 'github_token';

    /**
     * @var string
     * @api
     */
    public const THANKER_AUTHOR_NAME = 'thanker_author_name';

    /**
     * @api
     * @var string
     */
    public const KERNEL_PROJECT_DIR = 'kernel.project_dir';

    /**
     * @api
     * @var string
     */
    public const TWITTER_MAXIMAL_DAYS_IN_PAST = 'twitter_maximal_days_in_past';

    /**
     * @api
     * @var string
     */
    public const TWITTER_OAUTH_ACCESS_TOKEN_SECRET = 'twitter_oauth_access_token_secret';

    /**
     * @api
     * @var string
     */
    public const TWITTER_NAME = 'twitter_name';

    /**
     * @api
     * @var string
     */
    public const TWITTER_CONSUMER_SECRET = 'twitter_consumer_secret';

    /**
     * @api
     * @var string
     */
    public const TWITTER_CONSUMER_KEY = 'twitter_consumer_key';

    /**
     * @api
     * @var string
     */
    public const TWITTER_OAUTH_ACCESS_TOKEN = 'twitter_oauth_access_token';

    /**
     * @api
     * @var string
     */
    public const TWITTER_MINIMAL_GAP_IN_HOURS = 'twitter_minimal_gap_in_hours';
}
