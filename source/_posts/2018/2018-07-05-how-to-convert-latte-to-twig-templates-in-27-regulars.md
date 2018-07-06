---
id: 120
title: "How to Convert Latte to Twig in 27 Regular Expressions"
perex: |
    [Statie](https://statie.org) - a tool for generating static open-sourced website like [this blog](https://github.com/tomasvotruba/tomasvotruba.cz) or [Pehapkari.cz](https://github.com/pehapkari/pehapkari.cz) - runs on YAML and Symfony DI Container. That way it's easy to understand by the PHP community worldwide.
    <br><br>
    But there are some pitfalls left. Like templates - being [Latte](https://latte.nette.org/en/) the only one is a pity. Twig is often requested feature and one of the last big reasons not to use Statie.
    <br><br>
    Well, **it was**. Statie will support both [Twig](https://github.com/Symplify/Symplify/pull/892) and Latte since next version.
    <br><br>
    **Are you a Twig fan? As a side effect, I made 27 regular expression to handle 80 % of the Latte to Twig migration for you.**
tweet: "New Post on my Blog: ... #symfony #twig #latte #nettefw"
---

<img src="/assets/images/posts/2018/latte-twig/latte.png" class="mt-5 ml-5">
<img src="/assets/images/posts/2018/latte-twig/twig.jpg">

This regex saga started as an experiment on this site. I tested the Twig support in Statie here. This web had ~20 files in Latte and I needed them to be in Twig, so I know the Twig support works with all the edge cases I use on daily basis.

After the 5th change of code from `{$value}` to `{{ value }}` I started to have a weird feeling of being a robot or [a very slow AI](/blog/2018/05/03/how-do-you-treat-your-own-first-ai/). So I stopped to think a bit...

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

## Install and Use It

```bash
composer require symplify/latte-to-twig-converter:@dev --dev
vendor/bin/latte-to-twig-converter convert app/templates
```

It will find all the `*.twig` files, look for Latte code in it and if that matches, it will convert it to Twig. That way your `*.latte` files will keep Latte as long as you don't rename them.

I'd link you to [README](http://github.com/symplify/lattetotwigconverter) now for more, but actually, there is no more, this is all the usage.

## Twig To Latte Converter?

Are you Latte fan and do you want to migrate to Latte? Let's do this! There [is a test set of both engines](https://github.com/Symplify/Symplify/tree/a6b7c71a90fd984d2f31c5ed28957e2927608001/packages/LatteToTwigConverter/tests/LatteToTwigConverterSource), that can help you to start.

All you need to do is create **exact reverse of the match to replace rules**:

For example from [`Symplify\LatteToTwigConverter\CaseConverter\ConditionCaseConverter`](https://github.com/Symplify/Symplify/blob/master/packages/LatteToTwigConverter/src/CaseConverter/ConditionCaseConverter.php):

```php
# Twig to Latte
$content = Strings::replace($content, '{% else %}', '#{else}#');
```

↓

```php
# Latte to Twig
$content = Strings::replace($content, '#{else}#', '{% else %}');
```

or in more complex cases:

```php
# Twig to Latte
$content = Strings::replace(
    $content,
    '#{if isset\((.*?)\)}(.*?){\/if}#s',
    '{% if $1 is defined %}$2{% endif %}'
);
```

↓

```php
# Latte to Twig
$content = Strings::replace(
    $content,
    '#{% if (.*?) is defined %}(.*?){% endif %}#s',
    '{if isset($1)}$2{\/if}'
);
```

## Help to Work in Progress...

The rule set is no way complete since this site includes rather simple cases. Saying that, feel free to [create an issue or contribute to Symplify](https://github.com/symplify/symplify) with your use case. It might help others and others might help you.


<br><br>

## Go Out and Play

In the end I'd like to encourage you to do more of such experiments. I meet many programmers over meetups all across the Europe and they often don't have space - either the time in work or they won't allow themselves - to do such experiments.

In my open-source experience, these experiments give you the most knowledge. Instead of choosing the first solution because *I don't have time and I have to deliver the value*, I tried 3-4, tested them and then picked the one that worked the best. It was not the first one of course, and even if it was, I'd be much more convinces the solution is right instead of blindly believing it.

I learned a lot about regular expressions, about delimiters and universal capturing groups (easter egg: seek "http" in the source code and you'll find all the tips I found and found useful) thanks to [Stackoverflow]() and [regex101.com](https://regex101.com/). I also must thank to Jáchym Toušek and Ondra Mirtes with PHPStan who get me more engaged to regular expressions in useful and not-so-frustrating way.

<br>
<br>

**Free your mind and experiment! It's the best way to get better every iteration.**