<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\EventDispatcher;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TomasVotruba\Blog\Tests\Posts\Year2019\EventDispatcher\Source\Event\YoutuberNameEvent;
use TomasVotruba\Blog\Tests\Posts\Year2019\EventDispatcher\Source\EventSubscriber\EventAwareNotifyMeOnVideoPublishedEventSubscriber;

final class EventDispatchingWithEventTest extends TestCase
{
    public function test(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventAwareNotifyMeOnVideoPublishedEventSubscriber = new EventAwareNotifyMeOnVideoPublishedEventSubscriber();
        $eventDispatcher->addSubscriber($eventAwareNotifyMeOnVideoPublishedEventSubscriber);

        $this->assertSame('', $eventAwareNotifyMeOnVideoPublishedEventSubscriber->getYoutuberUserName());

        $youtuberNameEvent = new YoutuberNameEvent('Jirka Král');

        $eventDispatcher->dispatch($youtuberNameEvent);

        $this->assertSame(
            'Jirka Král',
            $eventAwareNotifyMeOnVideoPublishedEventSubscriber->getYoutuberUserName()
        );
    }
}
