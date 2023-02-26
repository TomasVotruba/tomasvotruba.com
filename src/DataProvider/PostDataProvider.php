<?php

declare(strict_types=1);

namespace TomasVotruba\Website\DataProvider;

use Symfony\Component\Finder\Finder;
use TomasVotruba\Website\EntityFactory\PostFactory;
use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\ValueObject\Post;

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

        $markdownFileFinder = Finder::create()
            ->name('*.md')
            ->in(__DIR__ . '/../../data');

        $fileInfos = iterator_to_array($markdownFileFinder->getIterator());
        $filePaths = array_keys($fileInfos);

        foreach ($filePaths as $filePath) {
            $post = $this->postFactory->createFromFilePath($filePath);

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
