<?php

declare(strict_types=1);

// allows using facades in data providers
// @see https://stackoverflow.com/a/26774924/1348344

use App\DependencyInjection\ContainerFactory;

require __DIR__ . '/../vendor/autoload.php';

$containerFactory = new ContainerFactory();
$containerFactory->create();
