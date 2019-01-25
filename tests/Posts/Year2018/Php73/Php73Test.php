<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\Php73;

use Nette\Utils\Json;
use Nette\Utils\JsonException as NetteJsonException;
use PHPUnit\Framework\TestCase;

final class Php73Test extends TestCase
{
    /**
     * @var string[]
     */
    private $items = [
        1 => 'a',
        2 => 'b',
    ];

    public function testCommadAfterTheLastArgument(): void
    {
        $newThree = function ($one, $two /* , now syntax error - uncomment when PHP 7.3 is ready */) {
            return $one + $two;
        };
        $this->assertSame(3, $newThree(1, 2));
    }

    public function testFirstLastKey(): void
    {
        // PHP 7.2- way
        reset($this->items);
        $firstKey = key($this->items);
        $this->assertSame(1, $firstKey);
        // PHP 7.3+ way
        $firstKey = array_key_first($this->items);
        $this->assertSame(1, $firstKey);
    }

    public function testLastKey(): void
    {
        $items = [
            1 => 'first',
            2 => 'last',
        ];

        // PHP 7.2- way
        end($items);
        $lastKey = key($items);
        $this->assertSame(2, $lastKey);
        // PHP 7.3+ way
        $lastKey = array_key_last($items);
        $this->assertSame(2, $lastKey);
    }

    public function testIsCountable(): void
    {
        $nullItems = null;
        $items = [];
        $this->assertFalse(is_countable($nullItems));
        $this->assertTrue(is_countable($items));
    }

    public function testNetteJsonException(): void
    {
        $notAJson = 'Jason';
        $this->expectException(NetteJsonException::class);
        Json::decode($notAJson);
    }

    public function testJsonException(): void
    {
        $notAJson = 'not a Json';
        $this->expectException('JsonException');
        json_decode($notAJson, false, 10, JSON_THROW_ON_ERROR);
    }
}
