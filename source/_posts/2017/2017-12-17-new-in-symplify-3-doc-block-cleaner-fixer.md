---
id: 68
title: "New in Symplify 3: DocBlock Cleaner Fixer"
perex: '''
    Focus on docblock has increased thanks to [PHP 7 scalar types](http://php.net/manual/en/migration70.new-features.php#migration70.new-features.scalar-type-declarations) and PHPStan with [intersection and union types](https://medium.com/@ondrejmirtes/phpstan-0-9-a-huge-leap-forward-1e9b0872d1cc). Thanks to that, more and more **docblocks become just visual noise** causing [cognitive overload](https://en.wikipedia.org/wiki/Cognitive_load).
    <br><br>
    Symplify 3 introduces a new help hand - **fixer that cleans doc block noise for you and makes your code more valuable to the reader**. 
'''
tweet: "Do you use PHP 7 and scalar types? Do you still see value in your docblocks? Which is useful and which is legacy? Symplify 3 introduces a new fixer, that helps you to clean the later! #codingstandard #phpcsfixer"
tweet_image: "/"
---


<blockquote class="blockquote text-center">
    ...when you program, you have to <strong>think about how someone will read your code</strong>, not just how a computer will interpret it.
    <footer class="blockquote-footer">
        <a href="https://en.wikiquote.org/wiki/Kent_Beck">Kent Beck</a>, creator of the Extreme Programming and Test Driven Development
    </footer>
</blockquote>

<br>

Do you find similar patterns in your code?
 
<br>


```php
/**
 * @param string $name
 */
public function addVisitor(string $name)
{
    // ...
}
```

or

```php
/**
 * @param InputInterface $input An Input instance
 */
public function getArguments(InputInterface $input)
{
    // ...
}
```

or

```php
/**
 * @param mixed $value
 */
public function addValue($value)
{
    // ...
}
```

or


```php
/**
 * @param array $items
 */
public function prependItems(array $items)
{
    // ...
}
```

or


```php
/**
 * @return Storage
 */
public function getStorage(): Storage
{
    // ...
}
```


Do you know what do they have in common?
**They only duplicate the typehint information and bring no extra value to the reader**.

*No big deal* you might say as code author. But your code is much more more read that written and...

<br>



<br>

## How to Remove <strike>Manually</strike> Automatically?

Cleaning every single case would be crazy. Luckily, we **live in CLI-refactoring generation**,
so all we need is Fixer - `Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer`.

<a href="https://github.com/Symplify/Symplify/pull/427" class="btn btn-dark btn-sm">
    <em class="fa fa-github fa-fw"></em> 
    See pull-request #427
</a>



### Tested on many Open-Source Projects

Docblocks don't have any standard format, so I **first tested this Fixer on handful of PHP open-source projects**: 

- [php-ml](https://github.com/php-ai/php-ml/pull/145) and [other PR](https://github.com/php-ai/php-ml/pull/146)
- [ReflectionDocBlock](https://github.com/phpDocumentor/ReflectionDocBlock/pull/137)
- and [Symfony](https://github.com/symfony/symfony/pull/24931)

Thanks to that Fixer now **covers dozens of edge cases** and is now read to use. 


## Challenge Your Code

**1. Install**

```bash
composer require symplify/easy-coding-standard --dev
```

**2. Create `easy-coding-standard.neon`**

```yaml
# easy-coding-standard
checkers:
    - Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer 

    # works best with these checkers, to remove empty docblock
    - Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer
    - Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer
```

**3. Run it and see the diff**

```bash
vendor/bin/ecs
```

@todo image



[More in the docs of Symplify\CodingStandard](https://github.com/Symplify/CodingStandard#block-comment-should-only-contain-useful-information-about-types)

<br>

Happy code valuation!