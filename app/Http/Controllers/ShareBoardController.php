<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Post;
use App\Repository\PostRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class ShareBoardController extends Controller
{
    public function __construct(
        private PostRepository $postRepository
    ) {
    }

    public function __invoke(): View
    {
        $randomPosts = $this->postRepository->fetchRandom(2);

        // @todo use GPT to create a tweet post suggestions
        // @todo use Parlalel run from Laravel new

        $yourApiKey = getenv('OPEN_AI_API_KEY');

        $client = \OpenAI::client($yourApiKey);

        // @todo do parlalel :)
        $tweets = [];
        foreach ($randomPosts as $randomPost) {
            $tweets[] = $this->createTweetForPost($client, $randomPost);
        }

        return \view('share_board', [
            'title' => 'Share board',
            'randomPosts' => $randomPosts,
        ]);
    }

    private function createTweetForPost(\OpenAI\Client $client, Post $randomPost)
    {
        /** @var Post $randomPost */
        $result = $client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello! I need help making a short engaging tweet for a blog post. Also add an emoji. No hash tags. Include link to the post in the end. Fit it 100-120 chars. Here is a blog post: ' . PHP_EOL . PHP_EOL . $randomPost->getContent() . PHP_EOL . PHP_EOL . 'Link: https://tomasvotruba.com/blog/' . $randomPost->getSlug(),
                ],
            ],
        ]);

        return $result->choices[0]->message->content;
    }
}
