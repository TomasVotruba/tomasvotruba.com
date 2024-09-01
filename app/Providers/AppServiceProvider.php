<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenAI;
use OpenAI\Client;
use Webmozart\Assert\Assert;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function (): Client {
            $openApiKey = getenv('OPENAI_API_KEY');
            // dev only
            if ($openApiKey === null) {
                $openApiKey = 'random_key';
            }

            return OpenAI::client($openApiKey);
        });

        $this->app->singleton(\Noweh\TwitterApi\Client::class, function (): \Noweh\TwitterApi\Client {

            $twitterAppId = getenv('TWITTER_APP_ID');
            $twitterApiKey = getenv('TWITTER_API_KEY');
            $twitterApiSecret = getenv('TWITTER_API_SECRET');
            $twitterAccessToken = getenv('TWITTER_ACCESS_TOKEN');
            $twitterAccessSecret = getenv('TWITTER_ACCESS_SECRET');
            $twitterBearerToken = getenv('TWITTER_BEARER_TOKEN');

            Assert::string($twitterAppId);
            Assert::string($twitterApiKey);
            Assert::string($twitterApiSecret);
            Assert::string($twitterAccessToken);
            Assert::string($twitterAccessSecret);
            Assert::string($twitterBearerToken);

            return new \Noweh\TwitterApi\Client([
                'account_id' => $twitterAppId,
                'access_token' => $twitterAccessToken,
                'access_token_secret' => $twitterAccessSecret,
                'consumer_key' => $twitterApiKey,
                'consumer_secret' => $twitterApiSecret,
                'bearer_token' => $twitterBearerToken,
                // Optional
                // 'free_mode' => false,
            ]);
        });
    }
}
