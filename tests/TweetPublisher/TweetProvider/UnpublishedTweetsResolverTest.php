<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\Tests\TweetProvider;

use TomasVotruba\Website\TweetPublisher\Tests\AbstractContainerAwareTestCase;
use TomasVotruba\Website\TweetPublisher\Tweet\Tweet;
use TomasVotruba\Website\TweetPublisher\TweetProvider\PostTweetsProvider;
use TomasVotruba\Website\TweetPublisher\TweetProvider\UnpublishedTweetsResolver;
use TomasVotruba\Website\TweetPublisher\TwitterApi\TwitterApiWrapper;

final class UnpublishedTweetsResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var UnpublishedTweetsResolver
     */
    private $unpublishedTweetsResolver;

    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postTweetsProvider = $this->container->get(PostTweetsProvider::class);
        $this->twitterApiWrapper = $this->container->get(TwitterApiWrapper::class);
        $this->unpublishedTweetsResolver = $this->container->get(UnpublishedTweetsResolver::class);
    }

    public function testUnpublishedTweetsResolver(): void
    {
        $unpublishedTweets = $this->unpublishedTweetsResolver->excludePublishedTweets(
            $this->postTweetsProvider->provide(),
            $this->twitterApiWrapper->getPublishedTweets()
        );

        foreach ($unpublishedTweets as $unpublishedTweet) {
            // this tweet is already published, so it should not be here
            $this->assertNotContains(
                'New post on my blog: Clean and Decoupled Controllers, Commands and Event Subscribers ',
                $unpublishedTweet->getText()
            );
        }

        $this->assertGreaterThanOrEqual(16, count($unpublishedTweets));
    }

    public function testPostTweetsProvider(): void
    {
        $postTweets = $this->postTweetsProvider->provide();
        $this->assertGreaterThanOrEqual(52, count($postTweets));

        $this->assertInstanceOf(Tweet::class, $postTweets[0]);
    }

    public function testTwitterApiWrapper(): void
    {
        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
        $this->assertGreaterThanOrEqual(41, count($publishedTweets));

        $this->assertInstanceOf(Tweet::class, $publishedTweets[0]);
    }
}
