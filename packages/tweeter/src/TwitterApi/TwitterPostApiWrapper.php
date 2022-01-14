<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use TomasVotruba\Tweeter\ValueObject\TwitterApi;

final class TwitterPostApiWrapper
{
    /**
     * @var string
     */
    private const UPDATE_URL = 'https://api.twitter.com/' . TwitterApi::API_VERSION . '/statuses/update.json';

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
