<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Json;

use Nette\Utils\Json;
use Symplify\SmartFileSystem\SmartFileSystem;

final class FileToJsonLoader
{
    private SmartFileSystem $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    public function load(string $file): array
    {
        $fileContent = $this->smartFileSystem->readFile($file);
        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }
}
