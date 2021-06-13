<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Contract;

use TomasVotruba\Tweeter\ValueObject\PostTweet;

interface TweetsProviderInterface
{
    /**
     * @return PostTweet[]
     */
    public function provide(): array;
}
