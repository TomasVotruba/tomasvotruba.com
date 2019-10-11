<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Json;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;

final class FileToJsonLoader
{
    public function load(string $file): array
    {
        return Json::decode(FileSystem::read($file), Json::FORCE_ARRAY);
    }
}
