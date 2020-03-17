<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Posts\Year2017\Ast;

use stdClass;

final class SomeClass
{
    public function someMethod(stdClass $value): void
    {
        $value->call();
    }
}
