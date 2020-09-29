<?php

declare(strict_types=1);

use Migrify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;

return [
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    PhpConfigPrinterBundle::class => ['all' => true],
];
