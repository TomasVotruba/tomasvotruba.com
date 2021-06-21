<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Nette\Utils\DateTime;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Tweeter\TweetEntityCompleter;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TomasVotruba\Tweeter\ValueObject\TwitterApi;
use TomasVotruba\Tweeter\ValueObjectFactory\PublishedTweetFactory;
use TomasVotruba\Website\ValueObject\Option;

final class TwitterPostApiWrapper
{
    /**
     * @var string
     */
    private const UPDATE_URL = 'https://api.twitter.com/' . TwitterApi::API_VERSION . '/statuses/update.json';

    /**
     * @var string
     * @see https://developer.twitter.com/en/docs/tweets/timelines/api-reference/get-statuses-user_timeline
     */
    private const TIMELINE_URL = 'https://api.twitter.com/' . TwitterApi::API_VERSION . '/statuses/user_timeline.json';

    /**
     * Dont go beyond 2019-01-01, way too old content
     *
     * @var int
     */
    private const FIRST_TWEET_ID = 1_080_944_076_000_817_154;

    /**
     * @var int
     */
    private const MAX_TWEET_PAGES = 4;

    private string $twitterName;

    /**
     * @var PublishedTweet[]
     */
    private array $publishedTweets = [];

    public function __construct(
        ParameterProvider $parameterProvider,
        private TwitterApiCaller $twitterApiCaller,
        private TwitterImageApiWrapper $twitterImageApiWrapper,
        private TweetEntityCompleter $tweetEntityCompleter,
        private PublishedTweetFactory $publishedTweetFactory
    ) {
        $this->twitterName = $parameterProvider->provideStringParameter(Option::TWITTER_NAME);
    }

    /**
     * @return PublishedTweet[]
     */
    public function getPublishedTweets(): array
    {
        if ($this->publishedTweets !== []) {
            return $this->publishedTweets;
        }

        $rawTweets = $this->getPublishedTweetsRaw();
        $rawTweets = $this->tweetEntityCompleter->completeOriginalUrlsToText($rawTweets);

        $tweets = $this->publishedTweetFactory->createFromRawTweets($rawTweets);
        $tweets = $this->filterPostTweets($tweets);

        // re-index
        $tweets = array_values($tweets);

        return $this->publishedTweets = $tweets;
    }

    public function publishTweet(string $status): void
    {
        $this->twitterApiCaller->callPost(self::UPDATE_URL, [
            'status' => $status,
        ]);
    }

    /**
     * Ref: https://developer.twitter.com/en/docs/media/upload-media/api-reference/post-media-upload and
     * https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/post-statuses-update.html "media_ids"
     */
    public function publishTweetWithImage(string $status, string $imageFile): void
    {
        $imageResponse = $this->twitterImageApiWrapper->uploadImage($imageFile);

        $this->twitterApiCaller->callPost(self::UPDATE_URL, [
            'status' => $status,
            'media_ids' => $imageResponse['media_id'],
        ]);
    }

    public function getHoursSinceLastTweet(): int
    {
        $publishedTweetsRaw = $this->getPublishedTweetsRaw();
        $lastRawTweet = reset($publishedTweetsRaw);

        $lastTweetDateTime = DateTime::from($lastRawTweet['created_at']);
        $nowDateTime = DateTime::from('now');

        $nowDateTimeInSeconds = strtotime((string) $nowDateTime);
        $lastTweetDateTimeInSeconds = strtotime((string) $lastTweetDateTime);

        $diffInSeconds = $nowDateTimeInSeconds - $lastTweetDateTimeInSeconds;

        return (int) ($diffInSeconds / DateTime::HOUR);
    }

    /**
     * @return mixed[]
     */
    private function getPublishedTweetsRaw(): array
    {
        $result = $this->getResult();
        $currentResult = $result;

        // simulate "paging"
        $page = 0;
        while (count($currentResult) > 0 && $page < self::MAX_TWEET_PAGES) {
            $lastResult = $result[array_key_last($result)];

            $maxId = $lastResult['id'];

            $currentResult = $this->getResult($maxId);
            $result = array_merge($result, $currentResult);

            ++$page;
        }

        return $result;
    }

    /**
     * @param PublishedTweet[] $tweets
     * @return PublishedTweet[]
     */
    private function filterPostTweets(array $tweets): array
    {
        return array_filter(
            $tweets,
            fn (PublishedTweet $publishedTweet) => str_contains($publishedTweet->getText(), 'tomasvotruba.com')
        );
    }

    /**
     * @return mixed[]
     */
    private function getResult(?int $maxId = null): array
    {
        $data = [
            // these will be filtered down by following conditions; at least number of posts
            'count' => 200,
            // we don't need any user info
            'trim_user' => true,
            // we don't need replies
            'exclude_replies' => true,
            // we don't need retweets
            'include_rts' => false,
            'since_id' => self::FIRST_TWEET_ID,
        ];

        if ($maxId !== null) {
            // we're way back in the past
            if ($maxId < self::FIRST_TWEET_ID) {
                return [];
            }

            $data['max_id'] = $maxId;
        }

        return $this->twitterApiCaller->callGet(self::TIMELINE_URL, '* from:' . $this->twitterName, $data);
    }
}
