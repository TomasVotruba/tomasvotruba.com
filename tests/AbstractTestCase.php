<?php

declare(strict_types=1);

namespace App\Tests;

use App\DependencyInjection\ContainerFactory;
use Illuminate\Container\Container;
use Illuminate\Foundation\Testing\TestCase;

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
        return $this->app->make($type);
    }
}
