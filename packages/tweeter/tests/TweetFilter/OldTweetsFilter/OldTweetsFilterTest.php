<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests\TweetFilter\OldTweetsFilter;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use TomasVotruba\Tweeter\TweetFilter\OldTweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\TweetsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class OldTweetsFilterTest extends AbstractKernelTestCase
{
    private TweetsProvider $postTweetsProvider;

    private OldTweetsFilter $oldTweetsFilter;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->postTweetsProvider = self::$container->get(TweetsProvider::class);
        $this->oldTweetsFilter = self::$container->get(OldTweetsFilter::class);
    }

    public function test(): void
    {
        if (! getenv('TWITTER_CONSUMER_KEY')) {
            $this->markTestSkipped('Run Twitter test only with access tokens.');
        }

        $postTweets = $this->postTweetsProvider->provide();
        $this->assertCount(1, $postTweets);

        $postTweets = $this->oldTweetsFilter->filter($postTweets);
        $this->assertCount(0, $postTweets);
    }
}
