<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use TomasVotruba\Tweeter\Exception\TwitterApi\TwitterApiException;
use TomasVotruba\Tweeter\TweetEntityCompleter;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TwitterAPIExchange;

final class TwitterApiWrapper
{
    /**
     * @var string
     */
    private const API_VERSION = '1.1';

    /**
     * @var string
     */
    private const IMAGE_UPLOAD_URL = 'https://upload.twitter.com/' . self::API_VERSION . '/media/upload.json';

    /**
     * @var string
     */
    private const UPDATE_URL = 'https://api.twitter.com/' . self::API_VERSION . '/statuses/update.json';

    /**
     * @var string
     * @see https://dev.twitter.com/rest/reference/get/statuses/user_timeline
     */
    private const TIMELINE_URL = 'https://api.twitter.com/' . self::API_VERSION . '/statuses/user_timeline.json';

    private string $twitterName;

    private TwitterAPIExchange $twitterAPIExchange;

    private TweetEntityCompleter $tweetEntityCompleter;

    /**
     * @var PublishedTweet[]
     */
    private array $rawTweets = [];

    public function __construct(
        string $twitterName,
        TwitterAPIExchange $twitterAPIExchange,
        TweetEntityCompleter $tweetEntityCompleter
    ) {
        $this->twitterName = $twitterName;
        $this->twitterAPIExchange = $twitterAPIExchange;
        $this->tweetEntityCompleter = $tweetEntityCompleter;
    }

    /**
     * @return PublishedTweet[]
     */
    public function getPublishedTweets(): array
    {
        if ($this->rawTweets !== []) {
            return $this->rawTweets;
        }

        $rawTweets = $this->getPublishedTweetsRaw();
        $rawTweets = $this->tweetEntityCompleter->completeOriginalUrlsToText($rawTweets);

        $tweets = [];
        foreach ($rawTweets as $fullTweet) {
            $tweets[] = new PublishedTweet($fullTweet['text']);
        }

        return $this->rawTweets = $tweets;
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
        $media = $this->callPost(self::IMAGE_UPLOAD_URL, [
            'media' => base64_encode(FileSystem::read($imageFile)),
        ]);

        $this->callPost(self::UPDATE_URL, [
            'status' => $status,
            'media_ids' => $media['media_id'],
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
        $result = $this->callGet(self::TIMELINE_URL, '* from:' . $this->twitterName, [
            // these will be filtered down by following conditions; at least number of posts
            'count' => 200,
            // we don't need any user info
            'trim_user' => true,
            // we don't need replies
            'exclude_replies' => true,
            // we don't need retweets
            'include_rts' => false,
            // this started at 2017-08-20, nothing before
            'since_id' => 824_225_319_879_987_203,
        ]);

        $this->ensureNoError($result);

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

        return $this->decodeJson($jsonResponse);
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function callGet(string $endPoint, string $query, array $data): array
    {
        $data['q'] = $query;

        $jsonResponse = $this->getTwitterApiExchange()
            ->setGetfield('?' . http_build_query($data))
            ->buildOauth($endPoint, 'GET')
            ->performRequest();

        return $this->decodeJson($jsonResponse);
    }

    /**
     * @param mixed[] $result
     */
    private function ensureNoError(array $result): void
    {
        if (! isset($result['errors'])) {
            return;
        }

        $errors = $result['errors'];

        throw new TwitterApiException(sprintf('Twitter API failed due to: "%s"', $errors[0]['message']));
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
}
