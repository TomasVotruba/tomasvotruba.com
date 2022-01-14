<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Nette\Utils\DateTime;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Tweeter\ValueObject\TwitterApi;
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

    /**
     * @var array<string, int|bool>
     */
    private const DEFAULT_CRITERIA = [
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

    public function __construct(
        private readonly TwitterApiCaller $twitterApiCaller,
        private readonly TwitterImageApiWrapper $twitterImageApiWrapper,
    ) {
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
}
