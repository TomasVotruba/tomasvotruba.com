<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\NeonYaml;

use Nette\Neon\Neon;
use PHPUnit\Framework\TestCase;

final class NeonTest extends TestCase
{
    public function testTabsVsSpaces(): void
    {
        $tabsContent = $this->decodeFile(__DIR__ . '/Neon/tabs.neon');
        $spacesContent = $this->decodeFile(__DIR__ . '/Neon/spaces.neon');

        $this->assertSame($tabsContent, $spacesContent);
        $this->assertSame(['address' => ['street' => '742 Evergreen Terrace']], $spacesContent);
    }

    public function testList(): void
    {
        $mixedListContent = $this->decodeFile(__DIR__ . '/Neon/mixed-list.neon');

        $this->assertSame([
            'services' => [
                0 => 'SomeService',
                'SomeService' => '~',
            ], ], $mixedListContent);
    }

    public function testMultiline(): void
    {
        $content = $this->decodeFile(__DIR__ . '/Neon/multi-lines.neon');
        $this->assertSame(['perex' => 'Multi' . PHP_EOL . 'line'], $content);
    }

    /**
     * @return mixed[]
     */
    private function decodeFile(string $file): array
    {
        return Neon::decode(file_get_contents(($file)));
    }
}
