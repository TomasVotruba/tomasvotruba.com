<?php

declare(strict_types=1);

namespace TomasVotruba\GithubContributorsThanker\ValueObject;

final class Option
{
    /**
     * @var string
     * @api
     */
    public const THANKER_REPOSITORY_NAME = 'thanker_repository_name';

    /**
     * @var string
     * @api
     */
    public const THANKER_AUTHOR_NAME = 'thanker_author_name';

    /**
     * @var string
     * @api
     */
    public const GITHUB_TOKEN = 'github_token';
}
