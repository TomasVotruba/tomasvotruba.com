<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Yaml;

use Nette\Utils\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class YamlFileDumper
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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

        $this->filesystem->dumpFile($filePath, $timestampComment . $yamlDump);
    }

    private function createTimestampComment(): string
    {
        $nowDateTime = (new DateTime())->format('Y-m-d H:i:s');

        return sprintf('# this file was generated on %s, do not edit it manually' . PHP_EOL, $nowDateTime);
    }
}
