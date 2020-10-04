<?php

declare(strict_types=1);

namespace TomasVotruba\Projects\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use League\Flysystem\Adapter\Local;

/**
 * @see https://github.com/Kevinrob/guzzle-cache-middleware#flysystem
 * @see https://stackoverflow.com/a/37379482/1348344
 */
final class CachedGuzzleFactory
{
    public function create(): Client
    {
        $handlerStack = HandlerStack::create();

        $localCacheMiddleware = new CacheMiddleware(
            new GreedyCacheStrategy(new FlysystemStorage(new Local(sys_get_temp_dir() . '/guzzle_cache')), 180)
        );
        $handlerStack->push($localCacheMiddleware, 'cache');

        return new Client([
            'handler' => $handlerStack,
        ]);
    }
}
