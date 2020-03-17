<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use Nette\Utils\DateTime;
use TomasVotruba\Tweeter\ValueObject\Tweet;

final class OldTweetsFilter
{
    private DateTime $maxPastDateTime;

    public function __construct(int $twitterMaximalDaysInPast)
    {
        $this->maxPastDateTime = DateTime::from('-' . $twitterMaximalDaysInPast . 'days');
    }

    /**
     * @param Tweet[] $postTweets
     * @return Tweet[]
     */
    public function filter(array $postTweets): array
    {
        return array_filter(
            $postTweets,
            fn (Tweet $postTweet): bool => $postTweet->getPostDateTime() >= $this->maxPastDateTime
        );
    }
}
