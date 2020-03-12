---
id: 168
title: "Function create_function() is Deprecated in PHP 7.2 - How to Migrate?"
perex: |
    If there would be "Miss Deprecation of PHP 7.2", `create_function()` would definitely win. They can be **very complex, tricky and very hard convert to PHP code**. Moreover without tests.
    <br><br>
    Do you have over 5 `create_function()` pieces in your code? Let's see how to migrate them.
tweet: "Learn new hack on my  🐘 #php blog: Function create_function() is Deprecated in #PHP 7.2 - How to Migrate?"
tweet_image: "/assets/images/posts/2018/create-function/sonic.png"
---

Why is this deprecated? Well, the string arguments of few functions behaves like `eval()` - that's [evil](https://stackoverflow.com/a/951868/1348344).

```php
<?php

create_function("$a", "return $a");
assert("$value == 5");
```

And what about quotes?

```php
<?php

create_function("$a", "return $a");
create_function('$a', 'return $a');
```

You don't want to think which if `"` or <code>`</code> [breaks the code](/blog/2018/10/11/hi-my-name-is-tom-conctat-vs-in-sprintf-vs-in-string-variables/#em-class-fas-fa-fw-fa-times-text-danger-fa-lg-em-em-class-fas-fa-fw-fa-times-text-danger-fa-lg-em-in-string-variable) - you want make code.

I think it's a good move, **so how do we refactor them?** Let's start with simple code:

```php
<?php

$callback = create_function('$matches', "return strtolower(\$matches[1]);");
```

**How would you refactor this to anonymous function?**

*(Pause for deep thinking...)*

```php
<?php

$callback = function ($matches) {
    return strtolower($matches[1]);
};
```

As you can see:

- 1st argument = function arguments
- 2nd argument = function body

Also, notice the `\$matches` → `$matches`. That's because quote escaping.

<br>

What about this one?

```php
<?php

create_function('$a,$b', "return \"min(b^2+a, a^2,b) = \".min(\$a*\$a+\$b,\$b*\$b+\$a);");
```

**How would you refactor this to anonymous function?**

*(Pause for deep thinking...)*

```php
<?php

function ($a, $b) {
    return "min(b^2+a, a^2,b) = " . min($a * $a + $b, $b * $b + $a);
};
```

The `"min(b^2+a, a^2,b) = "` is still a string, because it was escaped string in a string.

<img src="http://www.memefaces.com/static/images/memes/2868.jpg">

Too easy for you? Damn, you're smart.

<br>

Can you handle this?

```php
<?php

create_function('$b,$a', 'if (strncmp($a, $b, 3) == 0) return "** \"$a\" '.
            'and \"$b\"** Look the same to me! (looking at the first 3 chars)";');
```

**How would you refactor this to anonymous function?**

*(Pause for deep thinking...)*

```php
<?php

function ($b, $a) {
    if (strncmp($a, $b, 3) == 0) {
        return "** \"{$a}\" and \"{$b}\"** Look the same to me! (looking at the first 3 chars)";
    }
};
```

<br>

Ok, but you won't make this code snippet I found in Drupal/Wordpress:

```php
<?php

$this->map_xmlns_func = create_function('$p,$n', 'if(strlen($n[0])>0) $xd
    .= ":{$n[0]}"; return "{$xd}=\"{$n[1]}\"";');
```

**How would you refactor this to anonymous function?**

*(Pause for deep thinking...)*

```php
<?php

$this->map_xmlns_func = function ($p, $n) use ($xd) {
    if (strlen($n[0]) > 0) {
        $xd .= ":{$n[0]}";
    }
    return "{$xd}=\"{$n[1]}\"";
};
```

Who did forget `use ($xd)`? An anonymous function can't access variables that are not passed as arguments, so without this would crash.

<br>

And we could continue and continue with more *edgy* cases... but I bet **you're looking for a solution for your specific function**.
Well, you could ask on StackOverflow ([181 results and counting](https://www.google.com/search?q="deprecated"+"create_function"+"php"+"7.2"+site%3Astackoverflow.com+-preg_replace&oq="deprecated"+"create_function"+"php"+"7.2"+site%3Astackoverflow.com+-preg_replace)), but posting each of your 10 cases might get you banned. I have good news for you.

<img src="/assets/images/posts/2018/create-function/sonic.png" class="img-thumbnail">

<br>

Today in 2019 (almost there), you can instantly upgrade your code and it **will take you less time to install & run, then read this whole post so far**.

Just setup [Rector](https://github.com/rectorphp/rector) and run it:

```bash
composer require rector/rector --dev
```

```yaml
# rector.yaml
services:
    Rector\Php\Rector\FuncCall\CreateFunctionToAnonymousFunctionRector: ~
```

```bash
vendor/bin/rector process src
```

<br>

Happy instant upgrading!
