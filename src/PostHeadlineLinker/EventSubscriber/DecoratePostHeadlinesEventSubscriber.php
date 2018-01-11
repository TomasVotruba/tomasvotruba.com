<?php declare(strict_types=1);

namespace TomasVotruba\Website\PostHeadlineLinker\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\Renderable\File\PostFile;
use TomasVotruba\Website\PostHeadlineLinker\PostHeadlineLinker;

final class DecoratePostHeadlinesEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var PostHeadlineLinker
     */
    private $postHeadlineLinker;

    public function __construct(PostHeadlineLinker $postHeadlineLinker)
    {
        $this->postHeadlineLinker = $postHeadlineLinker;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [BeforeRenderEvent::class => 'decoratePostHeadlines'];
    }

    public function decoratePostHeadlines(BeforeRenderEvent $beforeRenderEvent): void
    {
        $objectsToRender = $beforeRenderEvent->getObjectsToRender();
        foreach ($objectsToRender as $objectToRender) {
            if (! $objectToRender instanceof PostFile) {
                continue;
            }

            $newContent = $this->postHeadlineLinker->processContent($objectToRender->getContent());
            $objectToRender->changeContent($newContent);
        }
    }
}
