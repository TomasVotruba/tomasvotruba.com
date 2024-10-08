<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Tests\AbstractTestCase;

final class PostRepositoryTest extends AbstractTestCase
{
    private PostRepository $postRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = $this->make(PostRepository::class);
    }

    public function testFetchAll(): void
    {
        $posts = $this->postRepository->fetchAll();
        $this->assertGreaterThan(200, count($posts));

        $this->assertContainsOnlyInstancesOf(Post::class, $posts);
    }

    public function testPostRoutes(): void
    {
        $posts = $this->postRepository->fetchAll();

        // limit the amount of posts, as the route tests are slow
        $last20Posts = array_slice($posts, 0, 20);

        foreach ($last20Posts as $last20Post) {
            // the url must be with localhost:8000
            $postTestUrl = 'https://localhost:8000/blog/' . $last20Post->getSlug();

            $response = $this->get($postTestUrl);
            $response->assertSuccessful();

            // detail of the post
            $response->assertSee('<div id="post">', false);
        }
    }
}
