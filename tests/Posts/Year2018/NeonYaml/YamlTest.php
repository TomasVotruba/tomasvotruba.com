<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\NeonYaml;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class YamlTest extends TestCase
{
    public function testTabsVsSpaces(): void
    {
        $spacesContent = Yaml::parseFile(__DIR__ . '/Yaml/spaces.yml');
        $this->assertSame(['address' => ['street' => '742 Evergreen Terrace']], $spacesContent);

        $this->expectException(ParseException::class);
        Yaml::parseFile(__DIR__ . '/Yaml/tabs.yml');
    }

    public function testMixedList(): void
    {
        $this->expectException(ParseException::class);
        Yaml::parseFile(__DIR__ . '/Yaml/mixed-list.yml');
    }

    public function testListAndArray(): void
    {
        $listContent = Yaml::parseFile(__DIR__ . '/Yaml/list.yml');
        $this->assertSame(['services' => ['SomeService', 'SomeService']], $listContent);

        $arrayContent = Yaml::parseFile(__DIR__ . '/Yaml/array.yml');
        $this->assertSame(['services' => ['SomeService' => null]], $arrayContent);
    }

    public function testMultiline(): void
    {
        $content = Yaml::parseFile(__DIR__ . '/Yaml/multi-lines.yml');
        $this->assertSame(['perex' => 'Multi' . PHP_EOL . 'line'], $content);

        $this->expectException(ParseException::class);
        Yaml::parseFile(__DIR__ . '/Yaml/multi-lines-incorrect.yml');
    }
}
