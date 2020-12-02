<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObjectFactory;

use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\ValueObject\Cluster;
use Webmozart\Assert\Assert;

final class ClusterFactory
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function create(string $name, string $description, array $postIds): Cluster
    {
        Assert::allInteger($postIds);

        $posts = [];
        foreach ($postIds as $postId) {
            $posts[] = $this->postRepository->get($postId);
        }

        return new Cluster($name, $description, $posts);
    }
}
