<?php

declare(strict_types=1);

namespace App\Socials;

use App\Entity\Post;
use App\Enum\GptModel;
use OpenAI\Client;

final readonly class PostTweetGenerator
{
    public function __construct(
        private readonly Client $openAIClient,
    ) {
    }

    public function generateTweet(Post $post): string
    {
        $createResponse = $this->openAIClient->chat()->create([
            'model' => GptModel::GPT_4O_MINI,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello! Please make a short engaging tweet for a blog post. Also add an emoji. No hash tags, no links, no quotes. Fit it 100-140 chars. Here is the blog post: ' . PHP_EOL . PHP_EOL . $post->getContent(),
                ],
            ],
        ]);

        return (string) $createResponse->choices[0]->message->content;
    }
}
