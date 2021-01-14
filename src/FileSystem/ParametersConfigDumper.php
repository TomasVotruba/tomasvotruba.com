<?php

declare(strict_types=1);

namespace TomasVotruba\Website\FileSystem;

use Symplify\PhpConfigPrinter\YamlToPhpConverter;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ParametersConfigDumper
{
    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private YamlToPhpConverter $yamlToPhpConverter
    ) {
    }

    /**
     * @param mixed[] $items
     */
    public function dumpPhp(string $key, array $items): SmartFileInfo
    {
        $data['parameters'][$key] = $items;

        $fileContent = $this->yamlToPhpConverter->convertYamlArray($data);
        $dumpFilePath = __DIR__ . '/../../config/_data/generated/' . $key . '.php';
        $this->smartFileSystem->dumpFile($dumpFilePath, $fileContent);

        return new SmartFileInfo($dumpFilePath);
    }
}
