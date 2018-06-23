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
    //$content = Strings::replace($content, '#{include ([^}]+)}#', '{% include $1 %}');

    // 3. suffix: {include "_snippets/menu.latte"} => {% include "_snippets/menu.twig" %}
    // 3. suffix: {include "_snippets/menu.latte", "data" => $data} => {% include "_snippets/menu.twig", "data" => $data %}
    // $content = Strings::replace($content, '#([A-Za-z_/"]+).latte#', '$1.twig');

    // 4. block: {block content}{/block} => {{ block content }}{/block}
    // $content = Strings::replace($content, '#{block ([A-Za-z_/"]+)}#', '{{ block $1 }}');
    // 5. /block: {/block} => {{ end block }}
    // $content = Strings::replace($content, '#{/block}#', '{{ endblock }}');

    // 6. {$post['relativeUrl']} => {{ post.relativeUrl }}
    // $content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]}#', '{{ $1.$2 }}');

    // 7. include var: {% include "_snippets/menu.latte", "data" => $data %} => {% include "_snippets/menu.twig", { "data": data } %}
    // see https://twig.symfony.com/doc/2.x/functions/include.html
    // single lines
    // ref https://regex101.com/r/uDJaia/1
//    $content = Strings::replace($content, '#({% include [^,]+,)([^}^:]+)(\s+%})#', function (array $match) {
//        $variables = explode(',', $match[2]);
//
//        $twigDataInString = ' { ';
//        $variableCount = count($variables);
//        foreach ($variables as $i => $variable) {
//            [$key, $value]  = explode('=>', $variable);
//            $key = trim($key);
//            $value = trim($value);
//            $value = ltrim($value, '$'); // variables do not start with
//
//            $twigDataInString .= $key . ': ' . $value;
//
//            // separator
//            if ($i < $variableCount - 1) {
//                $twigDataInString .= ', ';
//            }
//        }
//
//        $twigDataInString .= ' }';
//
//        return $match[1] . $twigDataInString . $match[3];
//    });

    //  {$post['updated_message']|noescape} =>  {{ post.updated_message | noescape }}
    // $content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]\|([^}]+)}#', '{{ $1.$2 | $3 }}');

    // {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
    // $content = Strings::replace($content, '#{sep}([^{]+){\/sep}#', '{% if loop.last == false %}$1{% endif %}');

    // https://regex101.com/r/XKKoUh/1/
    // {if isset($post['variable'])}...{/if} => {% if $post['variable'] is defined %}...{% endif %}
//    $content = Strings::replace($content, '#{if isset\(([^{]+)\)}(.*?){\/if}#s', '{% if $1 is defined %}$2{% endif %}');

    // {$post->getId()} => {{ post.getId() }}
//    $content = Strings::replace($content, '#{\$([\w]+)->([\w()]+)}#', '{{ $1.$2 }}');

    // {define sth}...{/define} => {% block sth %}...{% endblock %}
    $content = Strings::replace($content, '#{define (.*?)}(.*?){\/define}#s', '{% block $1 %}$2{% endblock %}');

    // {% if $post['deprecated'] => {% if $post.deprecated
    $content = Strings::replace($content, '#{% (\w+) \$([A-Za-z]+)\[\'([\A-Za-z]+)\'\]#', '{% $1 $2.$3');

    file_put_contents($twigFileInfo->getRealPath(), $content);
}
