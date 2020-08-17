---
id: 120
title: "How to Convert Latte Templates to Twig in 27 Regular Expressions"
perex: |
    Statie - a tool for generating static open-sourced website like this blog or [Pehapkari.cz](https://github.com/pehapkari/pehapkari.cz) - runs on YAML and Symfony DI Container. That way it's easy to understand by the PHP community worldwide.
    <br><br>
    But there are some pitfalls left. Like templates - being Latte the only one is a pity. Twig is often requested feature and one of the last big reasons not to use Statie.
    <br><br>
    Well, **it was**. Statie now supports [Twig](https://github.com/symplify/symplify/pull/892).
    <br><br>
    **Are you a Twig fan? As a side effect, I made 27 regular expression to handle 80 % of the Latte to Twig migration for you.**
tweet: "New Post on my Blog: How to Convert #Latte to #Twig in 27 Regular Expressions #nettefw #symfony"
tweet_image: "/assets/images/posts/2018/latte-twig/diff.png"

updated_since: "March 2019"
updated_message: |
    Do you want to convert your code from Latte to TWIG?<br>
    Go to **[Latte To Twig Converter package](https://github.com/symplify/lattetotwigconverter) on Github**.
---

<img src="/assets/images/posts/2018/latte-twig/latte.png" class="mt-5 ml-5">
<img src="/assets/images/posts/2018/latte-twig/twig.jpg">

This regex saga started as an experiment on this site. I tested the Twig support in Statie here. This web had ~20 files in Latte and I needed them to be in Twig, so I know the Twig support works with all the edge cases I use on daily basis.

After the 5th change of code from `{$value}` to `{{ value }}` I started to have a weird feeling of being a robot or [a very slow AI](/blog/2018/05/03/how-do-you-treat-your-own-first-ai). So I stopped to think a bit...

<blockquote class="blockquote text-center">
    "The question you should be asking isn't, "What do I want?" or "What are my goals?" but "What would excite me?"
    <footer class="blockquote-footer">Tim Ferriss</footer>
</blockquote>

And what excites me? **Investing 5 hours to automate 30-minutes manual work under 10 seconds, so no-one else will have to do that ever again.**

## Brother from Another Mother

The biggest difference between Latte, Twig, Smarty, Blade and all other templating engines is rather in the way they're written inside than in the syntax itself.

See Latte code:

```html
{foreach $values as $key => $value}
    {$value->getName()}

    {if isset($value['position'])}
        {$value['position']|noescape}
    {else}
        {var $noPosition = true}
    {/if}
{/foreach}
```

And see Twig code:

```twig
{% for key, value in values %}
    {{ value.getName() }}

    {% if value.position is defined %}
        {{ value.position|raw }}
    {% else %}
        {% set noPosition = true %}
    {% endif %}
{% endfor %}
```

And that's how 27 regular expressions solution was born step by step.

<br>

I'm not using `preg_replace`, but rather util method [`Nette\Utils\String::replace()`](https://github.com/nette/utils/blob/8eda0c33f798f7ce491715e9dae8797f191e9b00/src/Utils/Strings.php#L475) that handles compile-time errors, run-time errors and few more tricks in a fancy way. Do you want to use it too? Just run `composer update nette/utils`.

**Do you want to skip details and use it?** Jump to *[Install and Use It](#install-and-use-it)* section.

### 1. Block, Define, Include

- [in Latte](https://latte.nette.org/en/macros#toc-blocks)
- [in Twig](https://twig.symfony.com/doc/2.x/tags/block.html)

```php
use Nette\Utils\Strings;

// ...

// {block someBlock}...{/block} =>
// {% block anotherBlock %}...{% endblock %}
$content = Strings::replace($content, '#{block (\w+)}(.*?){\/block}#s', '{% block $1 %}$2{% endblock %}');

// {include "_snippets/menu.latte"} =>
// {% include "_snippets/menu.latte" %}
$content = Strings::replace($content, '#{include ([^}]+)}#', '{% include $1 %}');

// {define sth}...{/define} =>
// {% block sth %}...{% endblock %}
$content = Strings::replace($content, '#{define (.*?)}(.*?){\/define}#s', '{% block $1 %}$2{% endblock %}');

// {% include ... %} =>
// {{ block('...') }}
$content = Strings::replace($content, '#{% include \'?(\w+)\'? %}#', '{{ block(\'$1\') }}')
```

The most useful expression here is `(.*?)`. **It will capture everything until the next pattern**. In this case it keeps everything between 2 tags.

Also, **mind the `#s` modifier**. `(.*?)` matches newlines as well thanks to that. You can read more about modifiers in [PHP Documentation](http://php.net/manual/en/reference.pcre.pattern.modifiers.php)

The last notable tip I learned is `\w`, that matches `[a-zA-Z0-9_]` characters - usually all you need for variable names.

### 2. Capture, Set

- [in](https://latte.nette.org/en/macros#toc-variable-declaration) [Latte](https://latte.nette.org/en/macros#toc-capturing-to-variables)
- [in Twig](https://twig.symfony.com/doc/2.x/tags/set.html)

```php
use Nette\Utils\Strings;

// ...

// {var $var = $anotherVar} =>
// {% set var = anotherVar %}
$content = Strings::replace($content, '#{var \$?(.*?) = \$?(.*?)}#s', '{% set $1 = $2 %}');

// {capture $var}...{/capture} =>
// {% set var %}...{% endset %}
$content = Strings::replace($content, '#{capture \$(\w+)}(.*?){\/capture}#s', '{% set $1 %}$2{% endset %}');
```

As Twig doesn't use `$var` but just `var` as variable name, we need to get rid of the dollar `$` sign.

That's what this expression does:

```bash
\$?(.*?)
```

It will capture anything, but if there is `$`, it will remove it.

### 3. Comments

- [in Latte](https://latte.nette.org/en/macros)
- [in Twig](https://twig.symfony.com/doc/2.x/templates.html#comments)

```php
use Nette\Utils\Strings;

// ...

$content = Strings::replace($content, '#{\*(.*?)\*}#s', '{#$1#}');
```

### 4. Conditions, If, Ifset

- [in Latte](https://latte.nette.org/en/macros#toc-conditions)
- [in Twig](https://twig.symfony.com/doc/2.x/tags/if.html)

```php
use Nette\Utils\Strings;

// ...

// https://regex101.com/r/XKKoUh/1/
// {if isset($post['variable'])}...{/if} =>
// {% if $post['variable'] is defined %}...{% endif %}
$content = Strings::replace(
    $content,
    '#{if isset\((.*?)\)}(.*?){\/if}#s',
    '{% if $1 is defined %}$2{% endif %}'
);

// {ifset $post}...{/ifset} =>
// {% if $post is defined %}..{% endif %}
$content = Strings::replace($content, '#{ifset (.*?)}(.*?){\/ifset}#s', '{% if $1 is defined %}$2{% endif %}');

// {% if $post['deprecated'] =>
// {% if $post.deprecated
// https://regex101.com/r/XKKoUh/2
$content = Strings::replace($content, '#{% (\w+) \$([A-Za-z]+)\[\'([\A-Za-z]+)\'\]#', '{% $1 $2.$3');

// {if "sth"}..{/if} =>
// {% if "sth" %}..{% endif %}
// https://regex101.com/r/DrDSJf/1
$content = Strings::replace($content, '#{if (.*?)}(.*?){\/if}#s', '{% if $1 %}$2{% endif %}');

$content = Strings::replace($content, '#{else}#', '{% else %}');

$content = Strings::replace($content, '#{elseif (.*?)}#', '{% elseif $1 %}');
```

Nothing fancy here, just another great use case for `(.*?)` group.

### 5. Filters

- [in Latte](https://latte.nette.org/en/filters)
- [in Twig](https://twig.symfony.com/doc/2.x/filters/index.html)

```php
use Nette\Utils\Strings;

// ...

// {$post['updated_message']|noescape} =>
// {{ post.updated_message|noescape }}
$content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]\|([^}]+)}#', '{{ $1.$2|$3 }}');

// | noescape =>
// | raw
$content = Strings::replace($content, '#\|(\s+)?noescape#', '|$1raw');

// {% if count($var) %} =>
// {% if $var|length) %}
$content = Strings::replace($content, '#{% (.*?) count\(\$?(\w+)\)#', '{% $1 $2|length');
```

No surprises here.

### 6. Loops, While, For, Foreach

- [in Latte](https://latte.nette.org/en/macros#toc-loops)
- [in Twig](https://twig.symfony.com/doc/2.x/tags/for.html)

```php
use Nette\Utils\Strings;

// ...

// {foreach $values as $key => $value}...{/foreach} =>
// {% for key, value in values %}...{% endfor %}
$content = Strings::replace(
    $content,
    '#{foreach \$([()\w ]+) as \$([()\w ]+) => \$(\w+)}#',
    '{% for $2, $3 in $1 %}'
);

// {foreach $values as $value}...{/foreach} =>
// {% for value in values %}...{% endfor %}
$content = Strings::replace($content, '#{foreach \$([()\w ]+) as \$([()\w ]+)}#', '{% for $2 in $1 %}');
$content = Strings::replace($content, '#{/foreach}#', '{% endfor %}');

// {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
$content = Strings::replace($content, '#{sep}([^{]+){\/sep}#', '{% if loop.last == false %}$1{% endif %}');
```

### 7. Variables

- [in Latte](https://latte.nette.org/en/macros#toc-variable-printing)
- [in Twig](https://twig.symfony.com/doc/2.x/templates.html#variables)

```php
use Nette\Utils\Strings;

// ...

// {$google_analytics_tracking_id} =>
// {{ google_analytics_tracking_id }}
// {$google_analytics_tracking_id|someFilter} =>
// {{ google_analytics_tracking_id|someFilter }}
$content = Strings::replace($content, '#{\$(\w+)(\|.*?)?}#', '{{ $1$2 }}');

// {$post->getId()} =>
// {{ post.getId() }}
$content = Strings::replace($content, '#{\$([\w]+)->([\w()]+)}#', '{{ $1.$2 }}');

// {$post['relativeUrl']} =>
// {{ post.relativeUrl }}
$content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]}#', '{{ $1.$2 }}');

// {% if $post['rectify_post_id'] is defined %} =>
// {% if post.rectify_post_id is defined %}
$content = Strings::replace($content, '#({% \w+) \$(\w+)\[\'(\w+)\'\]#', '$1 $2.$3');
```

This was the simplest set so far. Always start with the easiest first.

### 8. Suffix

```php
use Nette\Utils\Strings;

// ...

// "_snippets/menu.latte" =>
// "_snippets/menu.twig"
$content = Strings::replace($content, '#([A-Za-z_/"]+).latte#', '$1.twig');
```

### 9. Include With Vars

This is the most complex solution in the set. What it does?

```diff
-{% include "_snippets/menu.latte", "data" => $data %}
+{% include "_snippets/menu.twig" with { "data": data } %}
```

It looks pretty simple, but I could not find an easier way to work with the nested array items.

```php
use Nette\Utils\Strings;

// include var:
// {% include "_snippets/menu.latte", "data" => $data %} =>
// {% include "_snippets/menu.twig", { "data": data } %}
// see https://twig.symfony.com/doc/2.x/functions/include.html
// single lines
// ref https://regex101.com/r/uDJaia/1
$content = Strings::replace($content, '#({% include [^,]+,)([^}^:]+)(\s+%})#', function (array $match) {
    $variables = explode(',', $match[2]);
    $twigDataInString = ' { ';
    $variableCount = count($variables);
    foreach ($variables as $i => $variable) {
        [$key, $value] = explode('=>', $variable);
        $key = trim($key);
        $value = trim($value);
        $value = ltrim($value, '$'); // variables do not start with
        $twigDataInString .= $key . ': ' . $value;
        // separator
        if ($i < $variableCount - 1) {
            $twigDataInString .= ', ';
        }
    }
    $twigDataInString .= ' }';

    return $match[1] . $twigDataInString . $match[3];
});

// {% include "sth", =>
// {% include "sth" with
$content = Strings::replace($content, '#({% include [^,{]+)(,)#', '$1 with');
```

What is here to take away? The `[^,{]+` set. It tells *find everything until the first `,` or `{` character*.
That way we catch everything we don't really work with.

## Install and Use It

```bash
composer require symplify/latte-to-twig-converter:@dev --dev
vendor/bin/latte-to-twig-converter convert app/templates
```

It will find all the `*.twig` files, look for Latte code in it and if that matches, it will convert it to Twig. That way your `*.latte` files will keep Latte as long as you don't rename them.

I'd link you to [README](http://github.com/symplify/lattetotwigconverter) now for more, but actually, there is no more, this is all the usage.

<br>

### How Does This Set Work in Real Project?

Just see [the PR on this website](https://github.com/TomasVotruba/tomasvotruba.com/pull/380)
or [the PR to Pehapkari.cz website](https://github.com/pehapkari/pehapkari.cz/pull/486).

<img src="/assets/images/posts/2018/latte-twig/diff.png" class="img-thumbnail">

## Twig To Latte Converter?

Are you Latte fan and do you want to migrate to Latte? Let's do this! There [is a test set of both engines](https://github.com/symplify/symplify/tree/a6b7c71a90fd984d2f31c5ed28957e2927608001/packages/LatteToTwigConverter/tests/LatteToTwigConverterSource), that can help you to start.

All you need to do is create the **exact reverse of the match to replace rules**:

For example from [`Symplify\LatteToTwigConverter\CaseConverter\ConditionCaseConverter`](https://github.com/symplify/symplify/blob/master/packages/LatteToTwigConverter/src/CaseConverter/ConditionCaseConverter.php):

```php
# Twig to Latte
use Nette\Utils\String;

$content = Strings::replace($content, '{% else %}', '#{else}#');
```

↓

```php
# Latte to Twig
use Nette\Utils\String;

$content = Strings::replace($content, '#{else}#', '{% else %}');
```

<br><br>

## Go Out and Play

In the end, I'd like to encourage you to do more of such experiments. I meet many programmers over meetups all across Europe and they often don't have space - either the time in work or they won't allow themselves - to do such experiments.

In my open-source experience, these experiments give you the most knowledge. Instead of choosing the first solution because *I don't have time and I have to deliver the value*, I tried 3-4, tested them and then picked the one that worked the best. It was not the first one of course, and even if it was, I'd be much more convinced the solution is right instead of just blindly believing it.

I learned a lot about regular expressions, about delimiters and universal capturing groups (easter egg: seek "http" in the source code and you'll find all the tips I found and found useful) thanks to StackOverflow and [regex101.com](https://regex101.com). I also must thank Jáchym Toušek and Ondra Mirtes with PHPStan who got me more engaged in regular expressions in a useful and not-so-frustrating way.

<br>
<br>

**Free your mind and experiment! It's the best way to get better every iteration.**
