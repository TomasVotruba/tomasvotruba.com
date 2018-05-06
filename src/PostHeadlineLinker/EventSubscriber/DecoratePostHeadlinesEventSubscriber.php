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
        $generatorFilesByType = $beforeRenderEvent->getGeneratorFilesByType();
        /** @var PostFile[] $postFiles */
        $postFiles = $generatorFilesByType[PostFile::class];

        foreach ($postFiles as $postFile) {
            $newContent = $this->postHeadlineLinker->processContent($postFile->getContent());

            $postFile->changeContent($newContent);
        }
    }
}
