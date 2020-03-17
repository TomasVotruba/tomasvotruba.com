<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Contract;

use TomasVotruba\Tweeter\ValueObject\Tweet;

interface TweetsProviderInterface
{
    /**
     * @return Tweet[]
     */
    public function provide(): array;
}
