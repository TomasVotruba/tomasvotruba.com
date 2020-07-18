<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Yaml;

use Nette\Utils\DateTime;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileSystem;
use TomasVotruba\Website\Exception\NotImplementedYetException;

final class GeneratedFilesDumper
{
    private string $projectDir;

    private SmartFileSystem $smartFileSystem;

    public function __construct(string $projectDir, SmartFileSystem $smartFileSystem)
    {
        $this->projectDir = $projectDir;
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @param mixed[] $items
     */
    public function dump(string $key, array $items, string $format): void
    {
        $data['parameters'][$key] = $items;

        if ($format === 'yaml') {
            $fileContent = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

            $dumpFilePath = $this->projectDir . '/config/_data/generated/' . $key . '.yaml';
            $timestampComment = $this->createTimestampComment();
            $this->smartFileSystem->dumpFile($dumpFilePath, $timestampComment . $fileContent);
        }

        if ($format === 'php') {
            throw new NotImplementedYetException();
        }
    }

    private function createTimestampComment(): string
    {
        return sprintf(
            '# this file was generated on %s, do not edit it manually' . PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s')
        );
    }
}
