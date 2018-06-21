#!/usr/bin/env php
<?php declare(strict_types=1);

use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/../vendor/autoload.php';

$sourceDirectory = __DIR__ . '/../source';

$twigFilesFinder = Finder::create()
    ->files()
    ->in($sourceDirectory)
    ->name('*.twig');

/** @var \Symfony\Component\Finder\SplFileInfo[] $twigFileInfos */
$twigFileInfos = iterator_to_array($twigFilesFinder->getIterator());

foreach ($twigFileInfos as $twigFileInfo) {
    dump($twigFileInfo->getRelativePathname());
    // rules

    $content = $twigFileInfo->getContents();

    // 1. variables: {$google_analytics_tracking_id} => {{ $google_analytics_tracking_id }}
    // $content = Strings::replace($content, '#{\$([a-z_]+)}#', '{{ $1 }}');

    // 2. include: {include "_snippets/menu.latte"} => {% include "_snippets/menu.latte" %}
    $content = Strings::replace($content, '#{include (["a-z_/.]+)}#', '{% include $1 %}');

    file_put_contents($twigFileInfo->getRealPath(), $content);
}
