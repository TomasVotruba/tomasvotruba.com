<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyEventDispatcher\Source\ValueObject\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class YoutuberNameEvent extends Event
{
    public function __construct(private string $youtuberName)
    {
    }

    public function getYoutuberName(): string
    {
        return $this->youtuberName;
    }
}
