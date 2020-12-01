<?php

declare(strict_types=1);

namespace TomasVotruba\SymfonyStaticDump\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Controller\BlogArchiveController;
use TomasVotruba\Blog\Repository\PostRepository;

final class BlogArchiveControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    public function __construct(private PostRepository $postRepository)
    {
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
