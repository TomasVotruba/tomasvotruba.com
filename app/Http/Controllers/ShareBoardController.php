<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\ValueObject\PostTweet;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use OpenAI\Client;

final class ShareBoardController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly Client $client,
    ) {
    }

    public function __invoke(): View
    {
        $randomPosts = $this->postRepository->fetchRandom(4);

        // @todo do parallel :)
        $postTweets = [];
        foreach ($randomPosts as $randomPost) {
            $tweet = $this->createTweetForPost($randomPost);
            $postTweets[] = new PostTweet($tweet, $randomPost);
        }

        return \view('share_board', [
            'title' => 'Share board',
            'postTweets' => $postTweets,
        ]);
    }

    private function createTweetForPost(Post $post): string
    {
        $createResponse = $this->client->chat()->create([
            // @see https://platform.openai.com/docs/models
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello! I need help making a short engaging tweet for a blog post. Also add an emoji. No hash tags, no links, no quotes. Fit it 100-120 chars. Here is a blog post: ' . PHP_EOL . PHP_EOL . $post->getContent(),
                ],
            ],
        ]);

        return (string) $createResponse->choices[0]->message->content;
    }
}
