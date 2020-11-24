<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests;

use TomasVotruba\Tweeter\TweetProvider\PostTweetsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class TweetsProviderTest extends AbstractTwitterTestCase
{
    private PostTweetsProvider $postTweetsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->postTweetsProvider = self::$container->get(PostTweetsProvider::class);

        $this->ensureEnvVariablesAreSet();
    }

    public function test(): void
    {
        $postTweets = $this->postTweetsProvider->provide();
        $this->assertGreaterThan(200, $postTweets);

        $oldestPost = $postTweets[array_key_last($postTweets)];

        $postDate = $oldestPost->getPostDateTimeInFormat('Y-m-d');
        $this->assertSame('2019-01-03', $postDate);
    }
}
