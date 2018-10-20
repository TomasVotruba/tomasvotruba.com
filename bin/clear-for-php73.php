<?php

use Nette\Utils\FileSystem;
use Nette\Utils\Json;

const COMPOSER_JSON = __DIR__ . '/../composer.json';

require __DIR__ . '/../vendor/autoload.php';

// read
$composerJson = Json::decode(FileSystem::read(COMPOSER_JSON), Json::FORCE_ARRAY);

// change
unset($composerJson['require-dev']['symplify/easy-coding-standard']);

// save
FileSystem::write(COMPOSER_JSON, Json::encode($composerJson));
