<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Tests\AbstractTestCase;
use OndraM\CiDetector\CiDetector;

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

    public function testLast(): void
    {
        $lastPosts = $this->postRepository->fetchLast(5);

        $this->assertCount(5, $lastPosts);
        $this->assertContainsOnlyInstancesOf(Post::class, $lastPosts);
    }

    public function testPostRoutes(): void
    {
        //$ciDetector = new CiDetector();
        //if ($ciDetector->isCiDetected()) {
        //    $this->markTestSkipped('Works only locally for now, ask Patricio for help');
        //}

        // limit the amount of posts, as the route tests are slow
        $posts = $this->postRepository->fetchLast(20);

        foreach ($posts as $post) {
            // the url must be with localhost:8000
            $postTestUrl = 'https://localhost:8000/blog/' . $post->getSlug();

            $response = $this->get($postTestUrl);
            $response->assertSuccessful();

            // detail of the post
            $response->assertSee('<div id="post">', false);
        }
    }
}
