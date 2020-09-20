<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyEventDispatcher;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyEventDispatcher\Source\EventSubscriber\OnVideoPublishedEventSubscriber;
use TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyEventDispatcher\Source\ValueObject\Event\YoutuberNameEvent;

final class EventDispatchingWithEventTest extends TestCase
{
    public function test(): void
    {
        $eventDispatcher = new EventDispatcher();

        $onVideoPublishedEventSubscriber = new OnVideoPublishedEventSubscriber();
        $eventDispatcher->addSubscriber($onVideoPublishedEventSubscriber);

        $this->assertSame('', $onVideoPublishedEventSubscriber->getYoutuberUserName());

        $youtuberNameEvent = new YoutuberNameEvent('Jirka Král');
        $eventDispatcher->dispatch($youtuberNameEvent);

        $this->assertSame('Jirka Král', $onVideoPublishedEventSubscriber->getYoutuberUserName());
    }
}
