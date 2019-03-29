<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2019\FinalMock\Listener;

use DG\BypassFinals;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Throwable;

/**
 * @see https://phpunit.readthedocs.io/en/8.0/extending-phpunit.html
 */
final class BypassFinalListener implements TestListener
{
    public function addError(Test $test, Throwable $throwable, float $time): void
    {
    }

    public function addWarning(Test $test, Warning $warning, float $time): void
    {
    }

    public function addFailure(Test $test, AssertionFailedError $assertionFailedError, float $time): void
    {
    }

    public function addIncompleteTest(Test $test, Throwable $throwable, float $time): void
    {
    }

    public function addRiskyTest(Test $test, Throwable $throwable, float $time): void
    {
    }

    public function addSkippedTest(Test $test, Throwable $throwable, float $time): void
    {
    }

    public function startTestSuite(TestSuite $testSuite): void
    {
    }

    public function endTestSuite(TestSuite $testSuite): void
    {
    }

    public function startTest(Test $test): void
    {
        BypassFinals::enable();
    }

    public function endTest(Test $test, float $time): void
    {
    }
}
