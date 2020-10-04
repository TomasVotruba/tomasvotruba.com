<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use Nette\Utils\DateTime;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Tweeter\ValueObject\Tweet;
use TomasVotruba\Website\ValueObject\Option as OptionAlias;

final class OldTweetsFilter
{
    private DateTime $maxPastDateTime;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $twitterMaximalDaysInPast = $parameterProvider->provideIntParameter(OptionAlias::TWITTER_MAXIMAL_DAYS_IN_PAST);

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
