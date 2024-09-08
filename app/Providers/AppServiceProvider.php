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
            $openApiKey = env('OPENAI_API_KEY');
            Assert::string($openApiKey, 'OPEN_API_KEY is missing in .env');
            return OpenAI::client($openApiKey);
        });


        $this->app->singleton(\Noweh\TwitterApi\Client::class, function (): \Noweh\TwitterApi\Client {
            $twitterAppId = env('TWITTER_APP_ID');
            $twitterApiKey = env('TWITTER_API_KEY');
            $twitterApiSecret = env('TWITTER_API_SECRET');
            $twitterAccessToken = env('TWITTER_ACCESS_TOKEN');
            $twitterAccessSecret = env('TWITTER_ACCESS_SECRET');
            $twitterBearerToken = env('TWITTER_BEARER_TOKEN');

            Assert::string($twitterAppId, '"TWITTER_APP_ID" env value is missing in .env');
            Assert::string($twitterApiKey, '"TWITTER_API_KEY" env value is missing in .env');
            Assert::string($twitterApiSecret, '"TWITTER_API_SECRET" env value is missing in .env');
            Assert::string($twitterAccessToken, '"TWITTER_ACCESS_TOKEN" env value is missing in .env');
            Assert::string($twitterAccessSecret, '"TWITTER_ACCESS_SECRET" env value is missing in .env');
            Assert::string($twitterBearerToken, '"TWITTER_BEARER_TOKEN" env value is missing in .env');

            return new \Noweh\TwitterApi\Client([
                'account_id' => $twitterAppId,
                'access_token' => $twitterAccessToken,
                'access_token_secret' => $twitterAccessSecret,
                'consumer_key' => $twitterApiKey,
                'consumer_secret' => $twitterApiSecret,
                'bearer_token' => $twitterBearerToken,
            ]);
        });
    }
}
