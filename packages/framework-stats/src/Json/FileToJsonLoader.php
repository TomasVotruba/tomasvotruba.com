<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Json;

use Nette\Utils\Json;
use Symplify\SmartFileSystem\SmartFileSystem;

final class FileToJsonLoader
{
    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @return mixed[]
     */
    public function load(string $file): array
    {
        $fileContent = $this->smartFileSystem->readFile($file);
        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }
}
