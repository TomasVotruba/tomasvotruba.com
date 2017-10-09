<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Nette\Utils\Finder;
use SplFileInfo;
use Symplify\Statie\Renderable\Configuration\ConfigurationDecorator;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Renderable\Routing\RouteFileDecorator;

final class PostsProvider
{
    /**
     * @var string
     */
    private $postSource;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    /**
     * @var RouteFileDecorator
     */
    private $routeFileDecorator;

    public function __construct(
        string $postSource,
        FileFactory $fileFactory,
        ConfigurationDecorator $configurationDecorator,
        RouteFileDecorator $routeFileDecorator
    ) {
        $this->fileFactory = $fileFactory;
        $this->configurationDecorator = $configurationDecorator;
        $this->postSource = $postSource;
        $this->routeFileDecorator = $routeFileDecorator;
    }

    /**
     * @return PostFile[]|AbstractFile[]
     */
    public function provide(): array
    {
        return $this->getPostsWithConfigurationFromSource($this->postSource);
    }

    /**
     * @return PostFile[]|AbstractFile[]
     */
    private function getPostsWithConfigurationFromSource(string $postSource): array
    {
        $files = $this->findMdFilesInDirectory($postSource);
        $posts = $this->fileFactory->createFromFileInfos($files);
        $this->configurationDecorator->decorateFiles($posts);
        $this->routeFileDecorator->decorateFiles($posts);

        return $posts;
    }

    /**
     * @return SplFileInfo[]
     */
    private function findMdFilesInDirectory(string $postSource): array
    {
        /** @var Finder $finder */
        $finder = Finder::findFiles('*.md')->from($postSource);

        return iterator_to_array($finder->getIterator());
    }
}
