<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Post;
use App\Repository\PostRepository;
use DateTimeInterface;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

final class RssController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(): Response
    {
        $posts = $this->postRepository->fetchAll();

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
