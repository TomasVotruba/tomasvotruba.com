<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Symplify\Statie\Application\StatieApplication;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\PostFile;

final class PostsProvider
{
    /**
     * @var StatieApplication
     */
    private $statieApplication;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $sourceDirectory;

    public function __construct(
        string $sourceDirectory,
        StatieApplication $statieApplication,
        Configuration $configuration
    ) {
        $this->statieApplication = $statieApplication;
        $this->configuration = $configuration;
        $this->sourceDirectory = $sourceDirectory;
    }

    /**
     * @return PostFile[]
     */
    public function provide(): array
    {
        $this->statieApplication->run(
            $this->sourceDirectory,
            __DIR__ . '/../../output',
            true
        );

        return $this->configuration->getOption('posts');
    }
}
