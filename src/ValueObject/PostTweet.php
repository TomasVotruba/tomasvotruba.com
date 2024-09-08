<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Entity\Post;

final readonly class PostTweet
{
    public function __construct(
        private string $tweet,
        private Post $post,
    ) {
    }

    public function getTweet(): string
    {
        return $this->tweet;
    }

    public function getPostThumbnail(): string
    {
        return '/thumbnail/' . $this->post->getClearTitle() . '.png';
    }

    public function getUrl(): string
    {
        return 'https://tomasvotruba.com/blog/' . $this->post->getSlug();
    }
}
