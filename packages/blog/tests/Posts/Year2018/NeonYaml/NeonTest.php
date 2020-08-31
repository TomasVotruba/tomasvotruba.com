<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\NeonYaml;

use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class NeonTest extends TestCase implements PostTestInterface
{
    /**
     * @dataProvider provideFilesToContent()
     * @param mixed[] $expectedContent
     */
    public function test(string $file, array $expectedContent): void
    {
        $this->assertSame($expectedContent, $this->decodeFile($file));
    }

    /**
     * @return mixed[][]
     */
    public function provideFilesToContent(): array
    {
        return [
            [
                __DIR__ . '/Neon/tabs.neon', [
                    'address' => [
                        'street' => '742 Evergreen Terrace',
                    ],
                ], ],
            [
                __DIR__ . '/Neon/spaces.neon', [
                    'address' => [
                        'street' => '742 Evergreen Terrace',
                    ],
                ], ],
            # arrays and lists
            [
                __DIR__ . '/Neon/mixed-list.neon', [
                    'services' => [
                        0 => 'SomeService',
                        'SomeService' => '~',
                    ],
                ], ],
            # multi-line
            [__DIR__ . '/Neon/multi-lines.neon', ['perex' => 'Multi' . PHP_EOL . 'line']],
        ];
    }

    public function getPostId(): int
    {
        return 83;
    }

    /**
     * @return mixed[]
     */
    private function decodeFile(string $file): array
    {
        return Neon::decode(FileSystem::read($file));
    }
}
