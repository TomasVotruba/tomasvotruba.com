<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyEventDispatcher\Source\ValueObject\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class YoutuberNameEvent extends Event
{
    private string $youtuberName;

    public function __construct(string $youtuberName)
    {
        $this->youtuberName = $youtuberName;
    }

    public function getYoutuberName(): string
    {
        return $this->youtuberName;
    }
}
