<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use App\Socials\PostTweetGenerator;
use App\ValueObject\PostTweet;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class ShareBoardController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly PostTweetGenerator $postTweetGenerator,
    ) {
    }

    public function __invoke(): View
    {
        $randomPosts = $this->postRepository->fetchRandom(4);

        // @todo do parallel :)
        $postTweets = [];
        foreach ($randomPosts as $randomPost) {
            $tweet = $this->postTweetGenerator->generateTweet($randomPost);
            $postTweets[] = new PostTweet($tweet, $randomPost);
        }

        return \view('share_board', [
            'title' => 'Share board',
            'postTweets' => $postTweets,
        ]);
    }
}
