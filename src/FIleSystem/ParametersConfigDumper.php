<?php

declare(strict_types=1);

namespace TomasVotruba\Website\FIleSystem;

use Migrify\PhpConfigPrinter\YamlToPhpConverter;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ParametersConfigDumper
{
    private SmartFileSystem $smartFileSystem;

    private YamlToPhpConverter $yamlToPhpConverter;

    public function __construct(SmartFileSystem $smartFileSystem, YamlToPhpConverter $yamlToPhpConverter)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->yamlToPhpConverter = $yamlToPhpConverter;
    }

    /**
     * @param mixed[] $items
     */
    public function dumpPhp(string $key, array $items): SmartFileInfo
    {
        $data['parameters'][$key] = $items;

        $fileContent = $this->yamlToPhpConverter->convertYamlArray($data);
        $dumpFilePath = getcwd() . '/config/_data/generated/' . $key . '.php';
        $this->smartFileSystem->dumpFile($dumpFilePath, $fileContent);

        return new SmartFileInfo($dumpFilePath);
    }
}
