<?php

declare(strict_types=1);

namespace TomasVotruba\SymfonyStaticDump\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Website\Controller\PostController;

final class PostControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
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
