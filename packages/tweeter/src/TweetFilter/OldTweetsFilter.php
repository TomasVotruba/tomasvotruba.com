<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Tweeter\ValueObject\PostTweet;
use TomasVotruba\Website\ValueObject\Option;

final class OldTweetsFilter
{
    /**
     * @var \DateTime|DateTimeImmutable
     */
    private readonly DateTimeInterface $maxPastDateTime;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $twitterMaximalDaysInPast = $parameterProvider->provideIntParameter(Option::TWITTER_MAXIMAL_DAYS_IN_PAST);

        $this->maxPastDateTime = DateTime::from('-' . $twitterMaximalDaysInPast . 'days');
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        return array_filter(
            $postTweets,
            fn (PostTweet $postTweet): bool => $postTweet->getDateTime() >= $this->maxPastDateTime
        );
    }
}
