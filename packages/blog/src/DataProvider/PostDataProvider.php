<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\DataProvider;

use Symplify\SmartFileSystem\Finder\SmartFinder;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Blog\ValueObjectFactory\PostFactory;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

final class PostDataProvider
{
    /**
     * @var string
     */
    private const POST_DIRECTORY = __DIR__ . '/../../data';

    public function __construct(
        private PostFactory $postFactory,
        private SmartFinder $smartFinder
    ) {
    }

    /**
     * @return Post[]
     */
    public function provide(): array
    {
        $posts = [];

        $fileInfos = $this->smartFinder->find([self::POST_DIRECTORY], '*.md');
        foreach ($fileInfos as $fileInfo) {
            $post = $this->postFactory->createFromFileInfo($fileInfo);
            if (isset($posts[$post->getId()])) {
                $message = sprintf('Post with id "%d" is duplicated', $post->getId());
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
            fn (Post $firstPost, Post $secondPost) => $secondPost->getDateTime() <=> $firstPost->getDateTime()
        );

        return $posts;
    }
}
