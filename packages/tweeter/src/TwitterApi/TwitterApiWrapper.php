<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use TomasVotruba\Tweeter\Exception\ShouldNotHappenException;
use TomasVotruba\Tweeter\Exception\TwitterApi\TwitterApiException;
use TomasVotruba\Tweeter\TweetEntityCompleter;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TomasVotruba\Tweeter\ValueObjectFactory\PublishedTweetFactory;
use TwitterAPIExchange;

final class TwitterApiWrapper
{
    /**
     * @var string
     */
    private const API_VERSION = '1.1';

    /**
     * @var string
     * @see https://developer.twitter.com/en/docs/media/upload-media/api-reference/post-media-upload
     */
    private const IMAGE_UPLOAD_URL = 'https://upload.twitter.com/' . self::API_VERSION . '/media/upload.json';

    /**
     * @var string
     */
    private const UPDATE_URL = 'https://api.twitter.com/' . self::API_VERSION . '/statuses/update.json';

    /**
     * @var string
     * @see https://developer.twitter.com/en/docs/tweets/timelines/api-reference/get-statuses-user_timeline
     */
    private const TIMELINE_URL = 'https://api.twitter.com/' . self::API_VERSION . '/statuses/user_timeline.json';

    /**
     * Dont go beyond 2019-01-01, way too old content
     */
    private const FIRST_TWEET_ID = 1_080_944_076_000_817_154;

    /**
     * @var int
     */
    private const MAX_TWEET_PAGES = 4;

    private string $twitterName;

    private TwitterAPIExchange $twitterAPIExchange;

    private TweetEntityCompleter $tweetEntityCompleter;

    /**
     * @var PublishedTweet[]
     */
    private array $publishedTweets = [];

    private PublishedTweetFactory $publishedTweetFactory;

    public function __construct(
        string $twitterName,
        TwitterAPIExchange $twitterAPIExchange,
        TweetEntityCompleter $tweetEntityCompleter,
        PublishedTweetFactory $publishedTweetFactory
    ) {
        $this->twitterName = $twitterName;
        $this->twitterAPIExchange = $twitterAPIExchange;
        $this->tweetEntityCompleter = $tweetEntityCompleter;
        $this->publishedTweetFactory = $publishedTweetFactory;
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
        $this->callPost(self::UPDATE_URL, ['status' => $status]);
    }

    /**
     * Ref: https://developer.twitter.com/en/docs/media/upload-media/api-reference/post-media-upload and
     * https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/post-statuses-update.html "media_ids"
     */
    public function publishTweetWithImage(string $status, string $imageFile): void
    {
        $headers = get_headers($imageFile, 1);
        if (! is_array($headers)) {
            throw new ShouldNotHappenException();
        }

        $fileSizeInBytes = $headers['Content-Length'];
        $mediaType = $headers['Content-Type'];

        $chunkLength = 5 * 1024 * 1024;

        $response = $this->callPost(self::IMAGE_UPLOAD_URL, [
            'command' => 'INIT',
            'total_bytes' => $fileSizeInBytes,
            'media_type' => $mediaType,
        ]);

        $mediaId = $response['media_id'];
        $this->runImageAppend($imageFile, $chunkLength, $mediaId);

        $response = $this->callPost(self::IMAGE_UPLOAD_URL, [
            'command' => 'FINALIZE',
            'media_id' => $mediaId,
        ]);

        $this->callPost(self::UPDATE_URL, [
            'status' => $status,
            'media_ids' => $response['media_id'],
        ]);
    }

    public function getDaysSinceLastTweet(): int
    {
        $rawTweets = $this->getPublishedTweetsRaw();
        $lastRawTweet = reset($rawTweets);

        $tweetPublishDate = DateTime::from($lastRawTweet['created_at']);
        $dateDiff = $tweetPublishDate->diff(DateTime::from('today'));

        return (int) $dateDiff->format('%a');
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
     * @param mixed[] $data
     * @return mixed[]
     */
    private function callPost(string $endPoint, array $data): array
    {
        $jsonResponse = $this->getTwitterApiExchange()
            ->setPostfields($data)
            ->buildOauth($endPoint, 'POST')
            ->performRequest();

        if ($jsonResponse === '') {
            return [];
        }

        $json = $this->decodeJson($jsonResponse);

        $this->ensureNoError($json);

        return $json;
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function callGet(string $endPoint, string $query, array $data): array
    {
        $data['q'] = $query;

        $getfield = '?' . http_build_query($data);

        $jsonResponse = $this->getTwitterApiExchange()
            ->setGetfield($getfield)
            ->buildOauth($endPoint, 'GET')
            ->performRequest();

        $json = $this->decodeJson($jsonResponse);
        $this->ensureNoError($json);

        return $json;
    }

    /**
     * @param mixed[] $result
     */
    private function ensureNoError(array $result): void
    {
        if (! isset($result['errors']) && ! isset($result['error'])) {
            return;
        }

        $errorMessage = $result['errors'][0]['message'] ?? $result['error'];

        throw new TwitterApiException(sprintf('Twitter API failed due to: "%s"', $errorMessage));
    }

    /**
     * The clone is needed, because setting setGetfield() prevents using setPostfields()
     */
    private function getTwitterApiExchange(): TwitterAPIExchange
    {
        return clone $this->twitterAPIExchange;
    }

    /**
     * @return mixed[]
     */
    private function decodeJson(string $jsonResponse): array
    {
        return Json::decode($jsonResponse, Json::FORCE_ARRAY);
    }

    /**
     * @param PublishedTweet[] $tweets
     * @return PublishedTweet[]
     */
    private function filterPostTweets(array $tweets): array
    {
        return array_filter($tweets, function (PublishedTweet $publishedTweet) {
            return Strings::match($publishedTweet->getText(), '#New post on#i');
        });
    }

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

        return $this->callGet(self::TIMELINE_URL, '* from:' . $this->twitterName, $data);
    }

    /**
     * @see https://www.askingbox.com/question/php-result-of-chunk-split-as-array
     */
    private function chunkSplitArray(string $content, int $chunklen): array
    {
        $result = [];
        $k = ceil(strlen($content) / $chunklen);

        for ($i = 0; $i < $k; $i++) {
            $result[] = substr($content, $i * $chunklen, $chunklen);
        }

        return $result;
    }

    private function runImageAppend(string $imageFile, int $chunkLength, int $mediaId): void
    {
        $based64EncodedBinaryFile = base64_encode(FileSystem::read($imageFile));

        // max size is 5 MB per chunk
        $chunks = $this->chunkSplitArray($based64EncodedBinaryFile, $chunkLength);

        foreach ($chunks as $index => $chunk) {
            // just 200 response
            $this->callPost(self::IMAGE_UPLOAD_URL, [
                'command' => 'APPEND',
                'media_id' => $mediaId,
                'media' => $chunk,
                'segment_index' => $index,
            ]);
        }
    }
}
