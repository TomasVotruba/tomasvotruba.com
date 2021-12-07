<?php

declare(strict_types=1);

namespace TomasVotruba\Website\SymfonyStaticDumper\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Website\Controller\PostController;

final class PostControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {
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
