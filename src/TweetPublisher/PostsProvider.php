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

    public function __construct(StatieApplication $statieApplication, Configuration $configuration)
    {
        $this->statieApplication = $statieApplication;
        $this->configuration = $configuration;
    }

    /**
     * @return PostFile[]
     */
    public function provide(): array
    {
        $this->statieApplication->run(__DIR__ . '/../../source', __DIR__ . '/../../output', true);

        return $this->configuration->getOption('posts');
    }
}
