<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use DateTimeInterface;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use TomasVotruba\Blog\Repository\PostRepository;

use TomasVotruba\Blog\ValueObject\Post;

final class RssController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(): View
    {
        $posts = $this->postRepository->fetchForRss();
        $response = \view('rss', [
            'posts' => $posts,
            'most_recent_post_date_time' => $this->getMostRecentPostDateTime($posts),
        ]);
        $response->headers->set('Content-type', 'text/xml');
        return $response;
    }

    /**
     * @param Post[] $posts
     */
    private function getMostRecentPostDateTime(array $posts): DateTimeInterface
    {
        $firstPostKey = array_key_first($posts);
        $firstPost = $posts[$firstPostKey];

        return $firstPost->getDateTime();
    }
}
