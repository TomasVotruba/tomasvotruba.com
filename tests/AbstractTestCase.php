<?php

declare(strict_types=1);

namespace App\Tests;

use App\DependencyInjection\ContainerFactory;
use Illuminate\Container\Container;
use Illuminate\Foundation\Testing\TestCase;
use Webmozart\Assert\Assert;

abstract class AbstractTestCase extends TestCase
{
    /**
     * This is magically invoked by parent setUp() call
     * @see \Illuminate\Foundation\Testing\TestCase::refreshApplication
     */
    public function createApplication(): Container
    {
        $containerFactory = new ContainerFactory();
        return $containerFactory->create();
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
