<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\Tests\TweetProvider;

use TomasVotruba\Website\TweetPublisher\Tests\AbstractContainerAwareTestCase;
use TomasVotruba\Website\TweetPublisher\Tweet\Tweet;
use TomasVotruba\Website\TweetPublisher\TweetProvider\PostTweetsProvider;

final class PostTweetsProviderTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postTweetsProvider = $this->container->get(PostTweetsProvider::class);
    }

    public function test(): void
    {
        $postTweets = $this->postTweetsProvider->provide();
        $this->assertGreaterThanOrEqual(51, $postTweets);

        $this->assertInstanceOf(Tweet::class, $postTweets[0]);
    }
}
