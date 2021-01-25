<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

abstract class AbstractTwitterTestCase extends AbstractKernelTestCase
{
    protected function ensureEnvVariablesAreSet(): void
    {
        // merge for Github Actions to work
        $envs = getenv();
        $_ENV = array_merge($_ENV, $envs);

        if ($_ENV['TWITTER_CONSUMER_KEY'] && $_ENV['TWITTER_CONSUMER_SECRET'] && $_ENV['TWITTER_OAUTH_ACCESS_TOKEN'] && $_ENV['TWITTER_OAUTH_ACCESS_TOKEN_SECRET']) {
            return;
        }

        $this->markTestSkipped(
            'To run Twitter tests run it with TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_OAUTH_ACCESS_TOKEN and TWITTER_OAUTH_ACCESS_TOKEN_SECRET; get them here - https://developer.twitter.com/en/apps/14147884'
        );
    }
}
