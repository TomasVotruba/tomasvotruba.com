<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Validation;

use TomasVotruba\Tweeter\Exception\TweetTooLongException;

final class TweetGuard
{
    /**
     * @var int
     */
    private const TWEET_MAX_LENGTH = 280;

    /**
     * @var int
     * @see https://dev.twitter.com/basics/tco#how-do-i-calculate-if-a-tweet-with-a-link-is-going-to-be-over-140-characters-or-not
     */
    private const SHORTENED_URL_LENGTH = 23;

    public function ensureTweetFitsAllowedLength(string $tweet): void
    {
        $tweetLength = mb_strlen($tweet);
        if ($tweetLength <= self::TWEET_MAX_LENGTH) {
            return;
        }

        throw new TweetTooLongException(sprintf(
            'Tweet message "%s" is too long, after adding its url. It has %d chars, shorten it under %d.',
            $tweet,
            $tweetLength,
            self::TWEET_MAX_LENGTH - self::SHORTENED_URL_LENGTH
        ));
    }
}
