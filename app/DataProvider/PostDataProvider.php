<?php

declare(strict_types=1);

namespace App\DataProvider;

use App\Entity\Post;
use App\EntityFactory\PostFactory;
use App\Exception\ShouldNotHappenException;
use Webmozart\Assert\Assert;

final class PostDataProvider
{
    public function __construct(
        private readonly PostFactory $postFactory,
    ) {
    }

    /**
     * @return Post[]
     */
    public function provide(): array
    {
        $posts = [];

        $markdownFilePaths = glob(__DIR__ . '/../../resources/posts/*/*.md');
        Assert::allString($markdownFilePaths);

        foreach ($markdownFilePaths as $markdownFilePath) {
            $post = $this->postFactory->createFromFilePath($markdownFilePath);

            if (isset($posts[$post->getId()])) {
                $message = sprintf('Post with id "%d" is duplicated. Increase it to higher one', $post->getId());
                throw new ShouldNotHappenException($message);
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
