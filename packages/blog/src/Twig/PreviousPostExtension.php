<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Twig;

use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\ValueObject\Post;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PreviousPostExtension extends AbstractExtension
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        $twigFunction = new TwigFunction('previous_post', function (Post $currentPost): ?Post {
            $posts = $this->postRepository->fetchAllEnglishNonDeprecated();

            foreach ($posts as $post) {
                if ($post->getId() >= $currentPost->getId()) {
                    continue;
                }

                return $post;
            }

            return null;
        });

        return [$twigFunction];
    }
}
