<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Yaml;

use Nette\Utils\DateTime;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileSystem;

final class YamlFileDumper
{
    private SmartFileSystem $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @param mixed[] $items
     */
    public function dumpAsParametersToFile(string $key, array $items, string $filePath): void
    {
        $data = [];
        $data['parameters'][$key] = $items;

        $yamlDump = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        $timestampComment = $this->createTimestampComment();

        $this->smartFileSystem->dumpFile($filePath, $timestampComment . $yamlDump);
    }

    private function createTimestampComment(): string
    {
        $nowDateTime = (new DateTime())->format('Y-m-d H:i:s');

        return sprintf('# this file was generated on %s, do not edit it manually' . PHP_EOL, $nowDateTime);
    }
}
