<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\Tests\TweetProvider;

use TomasVotruba\Website\TweetPublisher\Tests\AbstractContainerAwareTestCase;
use TomasVotruba\Website\TweetPublisher\TweetProvider\UnpublishedTweetsProvider;

final class UnpublishedTweetsProviderTest extends AbstractContainerAwareTestCase
{
    /**
     * @var UnpublishedTweetsProvider
     */
    private $unpublishedTweetsProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unpublishedTweetsProvider = $this->container->get(UnpublishedTweetsProvider::class);
    }

    public function test(): void
    {
        $unpublishedPostTweets = $this->unpublishedTweetsProvider->provide();
        $this->assertGreaterThanOrEqual(51, $unpublishedPostTweets);
    }
}
