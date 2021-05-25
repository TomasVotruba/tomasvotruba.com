<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\NeonYaml;

use Iterator;
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

    public function provideFilesToContent(): Iterator
    {
        yield [
            __DIR__ . '/Yaml/spaces.yml', [
                'address' => [
                    'street' => '742 Evergreen Terrace',
                ],
            ], ]
        ;
        # arrays
        yield [
            __DIR__ . '/Yaml/list.yml', [
                'services' => ['SomeService', 'SomeService'],
            ], ];
        yield [
            __DIR__ . '/Yaml/array.yml', [
                'services' => [
                    'SomeService' => null,
                ],
            ], ];
        # multi lines
        yield [__DIR__ . '/Yaml/multi-lines.yml', [
            'perex' => 'Multi' . PHP_EOL . 'line',
        ]];
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
     * @return Iterator<string[]>
     */
    public function provideFilesWithParseError(): Iterator
    {
        yield [__DIR__ . '/Yaml/tabs.yml'];
        yield [__DIR__ . '/Yaml/mixed-list.yml'];
        yield [__DIR__ . '/Yaml/multi-lines-incorrect.yml'];
    }

    public function getPostId(): int
    {
        return 83;
    }
}
