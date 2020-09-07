<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyEventDispatcher\Source\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyEventDispatcher\Source\Event\YoutuberNameEvent;

final class OnVideoPublishedEventSubscriber implements EventSubscriberInterface
{
    private string $youtuberUserName = '';

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [YoutuberNameEvent::class => 'notifyUserAboutVideo'];
    }

    public function notifyUserAboutVideo(YoutuberNameEvent $youtuberNameEvent): void
    {
        $this->youtuberUserName = $youtuberNameEvent->getYoutuberName();
    }

    public function getYoutuberUserName(): string
    {
        return $this->youtuberUserName;
    }
}
