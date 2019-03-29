<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2019\FinalMock\Hook;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

/**
 * @see https://phpunit.readthedocs.io/en/8.0/extending-phpunit.html
 */
final class BypassFinalHook implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}
