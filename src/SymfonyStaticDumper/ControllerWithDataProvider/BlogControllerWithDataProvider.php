<?php

declare(strict_types=1);

namespace TomasVotruba\Website\SymfonyStaticDumper\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Website\Controller\BlogController;

final class BlogControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    public function __construct(
        private PostRepository $postRepository
    ) {
    }

    public function getControllerClass(): string
    {
        return BlogController::class;
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
        foreach ($this->postRepository->fetchAllEnglish() as $post) {
            $years[] = $post->getYear();
        }

        $years = array_unique($years);
        sort($years);

        // remove the last one
        array_pop($years);

        return $years;
    }
}
