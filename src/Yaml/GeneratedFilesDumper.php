<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Yaml;

use Nette\Utils\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class GeneratedFilesDumper
{
    private string $projectDir;

    private Filesystem $filesystem;

    public function __construct(string $projectDir, Filesystem $filesystem)
    {
        $this->projectDir = $projectDir;
        $this->filesystem = $filesystem;
    }

    /**
     * @param mixed[] $items
     */
    public function dump(string $key, array $items): void
    {
        $data['parameters'][$key] = $items;

        $yamlDump = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        $dumpFilePath = $this->projectDir . '/config/_data/generated/' . $key . '.yaml';
        $timestampComment = $this->createTimestampComment();

        $this->filesystem->dumpFile($dumpFilePath, $timestampComment . $yamlDump);
    }

    private function createTimestampComment(): string
    {
        return sprintf(
            '# this file was generated on %s, do not edit it manually' . PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s')
        );
    }
}
