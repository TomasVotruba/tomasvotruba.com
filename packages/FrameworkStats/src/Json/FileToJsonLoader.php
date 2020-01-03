<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Json;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;

final class FileToJsonLoader
{
    public function load(string $file): array
    {
        $fileContent = FileSystem::read($file);

        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }
}
