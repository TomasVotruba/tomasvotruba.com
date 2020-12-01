<?php

declare(strict_types=1);

namespace TomasVotruba\SymfonyStaticDump\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Controller\PostController;
use TomasVotruba\Blog\Repository\PostRepository;

final class PostControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function getControllerClass(): string
    {
        return PostController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        $slugs = [];

        foreach ($this->postRepository->getPosts() as $post) {
            $slugs[] = $post->getSlug();
        }

        return $slugs;
    }
}
