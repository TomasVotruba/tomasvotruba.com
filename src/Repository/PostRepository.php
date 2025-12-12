<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\EntityFactory\PostFactory;
use App\Exception\ShouldNotHappenException;
use Nette\Utils\Strings;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

/**
 * @see \App\Tests\Repository\PostRepositoryTest
 */
final class PostRepository
{
    /**
     * @var Post[]
     */
    private array $posts = [];

    public function __construct(
        private readonly PostFactory $postFactory
    ) {
        $this->posts = $this->loadPostsFromStorage();
    }

    /**
     * @return Post[]
     */
    public function fetchAll(): array
    {
        return $this->posts;
    }

    public function getBySlug(string $slug): Post
    {
        $slug = rtrim($slug, '/');
        foreach ($this->posts as $post) {
            if ($post->getAlias() === $slug) {
                return $post;
            }

            $postSlug = rtrim($post->getSlug(), '/');
            if ($postSlug === $slug) {
                return $post;
            }
        }

        throw new NotFoundHttpException(sprintf('Post for slug "%s" was not found.', $slug));
    }

    /**
     * @return Post[]
     */
    private function loadPostsFromStorage(): array
    {
        $posts = [];

        $markdownFilePaths = glob(__DIR__ . '/../../resources/posts/*/*.md');
        Assert::allString($markdownFilePaths);

        foreach ($markdownFilePaths as $markdownFilePath) {
            $post = $this->postFactory->createFromFilePath($markdownFilePath);

            // ensure ids are unique
            if (isset($posts[$post->getId()])) {
                $errorMessage = sprintf(
                    'Post id "%d" already exists.
                    Check "%s" and increase it to "%d"',
                    $post->getId(),
                    Strings::after($markdownFilePath, '/', -1),
                    $post->getId() + 1
                );
                throw new ShouldNotHappenException($errorMessage);
            }

            $posts[$post->getId()] = $post;
        }

        return $this->sortByDateTime($posts);
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function sortByDateTime(array $posts): array
    {
        uasort(
            $posts,
            static fn (Post $firstPost, Post $secondPost): int => $secondPost->getDateTime() <=> $firstPost->getDateTime()
        );

        return $posts;
    }
}
