<?php

declare(strict_types=1);

use DG\BypassFinals;

require __DIR__ . '/../vendor/autoload.php';

// allow mocking "final", @see https://phpfashion.com/how-to-mock-final-classes
BypassFinals::enable();
