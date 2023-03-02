<?php

declare(strict_types=1);

namespace App\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\Request;
use Webmozart\Assert\Assert;

abstract class AbstractTestCase extends TestCase
{
    /**
     * This is magically invoked by parent setUp() call
     * @see \Illuminate\Foundation\Testing\TestCase::refreshApplication
     */
    public function createApplication(): Application
    {
        /** @var Application $application */
        $application = require __DIR__ . '/../bootstrap/app.php';

        /** @var Kernel $kernel */
        $kernel = $application->make(Kernel::class);
        $kernel->bootstrap();

        // @todo ask patricio if this can be done simpler
        // @todo set host to localhsot:8000 for test :)

        // setup for route, see https://chat.openai.com/chat/2535e131-d527-42f6-b7f4-a45fd951095
        $request = new Request();
        $application->instance('request', $request);

        return $application;
    }

    /**
     * @template TType as object
     * @param class-string<TType> $type
     * @return TType
     */
    protected function make(string $type): object
    {
        $service = $this->app->make($type);
        Assert::isInstanceOf($service, $type);

        return $service;
    }
}
