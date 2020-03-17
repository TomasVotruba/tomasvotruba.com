<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use TomasVotruba\Tweeter\TweetProvider\TweetsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class TweetsProviderTest extends AbstractKernelTestCase
{
    private TweetsProvider $postTweetsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->postTweetsProvider = self::$container->get(TweetsProvider::class);
    }

    public function test(): void
    {
        $postTweets = $this->postTweetsProvider->provide();

        $this->assertCount(1, $postTweets);

        $postTweet = $postTweets[0];

        $postDate = $postTweet->getPostDateTime()->format('Y-m-d');
        $this->assertSame('2018-10-30', $postDate);
    }
}
