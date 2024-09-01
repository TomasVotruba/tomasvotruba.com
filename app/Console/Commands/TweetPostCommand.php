<?php

declare(strict_types=1);

namespace App\Console\Commands;

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
        private Client $twitterClient,
    ) {

        parent::__construct();
    }

    public function handle()
    {
        $twitterResponse = $this->twitterClient->tweet()->create()->performRequest([
            'text' => 'Test Tweet... ',
        ]);

        $this->info('[DRY-RUN] Tweet is done!');
    }
}
