<?php

declare(strict_types=1);

namespace App\Tests;

use App\DependencyInjection\ContainerFactory;
use Illuminate\Container\Container;
use Illuminate\Foundation\Testing\TestCase;
use Override;

abstract class AbstractTestCase extends TestCase
{
    /**
     * This is magically invoked by parent setUp() call
     * @see \Illuminate\Foundation\Testing\TestCase::refreshApplication
     */
    #[Override]
    final public function createApplication(): Container
    {
        $containerFactory = new ContainerFactory();
        return $containerFactory->create();
    }

    /**
     * @template TType as object
     * @param class-string<TType> $type
     * @return TType
     */
    final protected function make(string $type): object
    {
        return $this->app->make($type);
    }
}
