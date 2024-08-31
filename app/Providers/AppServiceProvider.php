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
            Assert::string($openApiKey);

            return OpenAI::client($openApiKey);
        });
    }
}
