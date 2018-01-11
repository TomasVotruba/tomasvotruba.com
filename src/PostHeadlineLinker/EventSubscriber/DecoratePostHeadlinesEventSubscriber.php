<?php declare(strict_types=1);

namespace TomasVotruba\Website\PostHeadlineLinker\EventSubscriber;

use Nette\Utils\Strings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\Renderable\File\PostFile;

final class DecoratePostHeadlinesEventSubscriber implements EventSubscriberInterface
{
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

            $newContent = $this->decorateHeadlinesWithAnchors($objectToRender->getContent());
            $objectToRender->changeContent($newContent);
        }
    }

    private function decorateHeadlinesWithAnchors(string $htmlContent): string
    {
        return Strings::replace($htmlContent, '#<h(?<level>[1-6])>(?<title>.*?)<\/h[1-6]>#', function (array $result): string {
//            [$original, $headlineLevel, $headline] = $result;
//
//            dump($result);
//            die;

            $headlineId = Strings::webalize($result['title']);

            return sprintf(
                '<h%s id="%s"><a class="anchor" href="#%s">%s</a></h%s>',
                $result['level'],
                $headlineId,
                $headlineId,
                $result['title'],
                $result['level']
            );
        });
    }
}
