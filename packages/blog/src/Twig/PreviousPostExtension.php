<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Twig;

use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\ValueObject\Post;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PreviousPostExtension extends AbstractExtension
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        $previousPostFunction = new TwigFunction('previous_post', function (Post $currentPost): ?Post {
            $posts = $this->postRepository->fetchAllEnglishNonDeprecated();

            foreach ($posts as $post) {
                if ($post->getId() >= $currentPost->getId()) {
                    continue;
                }

                return $post;
            }

            return null;
        });

        return [$previousPostFunction];
    }
}
