<?php

declare(strict_types=1);

namespace TomasVotruba\Projects\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use League\Flysystem\Adapter\Local;

/**
 * @see https://github.com/Kevinrob/guzzle-cache-middleware#flysystem
 */
final class CachedGuzzleFactory
{
    public function create(): Client
    {
        $stack = HandlerStack::create();

        $localCacheMiddleware = new CacheMiddleware(
            new PrivateCacheStrategy(new FlysystemStorage(new Local(sys_get_temp_dir() . '/guzzle_cache')))
        );
        $stack->push($localCacheMiddleware, 'cache');

        return new Client(['handler' => $stack]);
    }
}
