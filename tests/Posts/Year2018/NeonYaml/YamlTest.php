<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\NeonYaml;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class YamlTest extends TestCase
{
    /**
     * @dataProvider provideFilesToContent()
     * @param mixed[] $expectedContent
     */
    public function test(string $file, array $expectedContent): void
    {
        $this->assertSame($expectedContent, Yaml::parseFile($file));
    }

    /**
     * @return mixed[][]
     */
    public function provideFilesToContent(): array
    {
        return [
            [__DIR__ . '/Yaml/spaces.yml', ['address' => ['street' => '742 Evergreen Terrace']]],
            # arrays
            [__DIR__ . '/Yaml/list.yml', ['services' => ['SomeService', 'SomeService']]],
            [__DIR__ . '/Yaml/array.yml', ['services' => ['SomeService' => null]]],
            # multi lines
            [__DIR__ . '/Yaml/multi-lines.yml', ['perex' => 'Multi' . PHP_EOL . 'line']],
        ];
    }

    /**
     * @dataProvider provideFilesWithParseError()
     */
    public function testParseErrors(string $file): void
    {
        $this->expectException(ParseException::class);
        Yaml::parseFile($file);
    }

    /**
     * @return string[][]
     */
    public function provideFilesWithParseError(): array
    {
        return [
            [__DIR__ . '/Yaml/tabs.yml'],
            [__DIR__ . '/Yaml/mixed-list.yml'],
            [__DIR__ . '/Yaml/multi-lines-incorrect.yml'],
        ];
    }
}
