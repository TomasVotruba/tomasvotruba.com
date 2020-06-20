<?php

declare(strict_types=1);

use DG\BypassFinals;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// load local envs
$envFiles = [__DIR__ . '/../.env', __DIR__ . '/../.env.local'];

$existingEnvFiles = array_filter($envFiles, 'is_file');
(new Dotenv())->load(... $existingEnvFiles);

// allow mocking "final", @see https://phpfashion.com/how-to-mock-final-classes
BypassFinals::enable();
