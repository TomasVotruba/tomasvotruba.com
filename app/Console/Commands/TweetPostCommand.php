<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repository\PostRepository;
use Illuminate\Console\Command;
use Noweh\TwitterApi\Client;

final class TweetPostCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:tweet-post';

    /**
     * @var string
     */
    protected $description = 'Tweet daily post';

    public function __construct(
        private readonly Client $twitterClient,
        private readonly PostRepository $postRepository,
    ) {

        parent::__construct();
    }

    public function handle(): void
    {
        $randomPost = $this->postRepository->fetchRandom(1);

        $twitterResponse = $this->twitterClient->tweet()->create()->performRequest([
            'text' => 'Test Tweet... ',
        ]);

        $this->info('[DRY-RUN] Tweet is done!');
    }
}
