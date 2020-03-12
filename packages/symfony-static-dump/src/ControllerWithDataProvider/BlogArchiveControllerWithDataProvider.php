<?php

declare(strict_types=1);

namespace TomasVotruba\SymfonyStaticDump\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Website\Controller\BlogArchiveController;

final class BlogArchiveControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getControllerClass(): string
    {
        return BlogArchiveController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return int[]
     */
    public function getArguments(): array
    {
        $years = [];
        foreach ($this->postRepository->fetchAllEnglishNonDeprecated() as $post) {
            $years[] = $post->getYear();
        }

        $years = array_unique($years);
        sort($years);

        // remove the last one
        array_pop($years);

        return $years;
    }
}
