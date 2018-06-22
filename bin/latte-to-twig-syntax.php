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
    // $content = Strings::replace($content, '#{\$([A-Za-z_]+)}#', '{{ $1 }}');

    // 2. include: {include "_snippets/menu.latte"} => {% include "_snippets/menu.latte" %}
    $content = Strings::replace($content, '#{include ([^}]+)}#', '{% include $1 %}');

    // 3. suffix: {include "_snippets/menu.latte"} => {% include "_snippets/menu.twig" %}
    // 3. suffix: {include "_snippets/menu.latte", "data" => $data} => {% include "_snippets/menu.twig", "data" => $data %}
    // $content = Strings::replace($content, '#([A-Za-z_/"]+).latte#', '$1.twig');

    // 4. block: {block content}{/block} => {{ block content }}{/block}
    // $content = Strings::replace($content, '#{block ([A-Za-z_/"]+)}#', '{{ block $1 }}');
    // 5. /block: {/block} => {{ end block }}
    // $content = Strings::replace($content, '#{/block}#', '{{ endblock }}');

    // 6. {$post['relativeUrl']} => {{ post.relativeUrl }}
    // $content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]}#', '{{ $1.$2 }}');

    // 7. include var: {% include "_snippets/menu.latte", "data" => $data} => {% include "_snippets/menu.twig", {"data": $data} %}
    // @todo
    $content = Strings::replace($content, '#{include ([A-Za-z_-"]+), ___}#', '{{ "$1" ,$2 }}');


    file_put_contents($twigFileInfo->getRealPath(), $content);
}
