<?php

declare(strict_types=1);

namespace App\Http\Controller;

use DateTimeInterface;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use TomasVotruba\Website\Repository\PostRepository;
use TomasVotruba\Website\ValueObject\Post;

final class RssController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(): Response
    {
        $posts = $this->postRepository->fetchForRss();

        $contents = view('rss', [
            'posts' => $posts,
            'most_recent_post_date_time_stamp' => $this->getMostRecentPostDateTime($posts)
                ->format('r'),
        ])->render();

        return response($contents, 200, [
            'Content-type' => 'text/xml',
        ]);
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
