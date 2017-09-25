---
id: 54
layout: post
title: "How to write Open-Source in PHP 3: Deprecating Code"
perex: '''
    Humans, world and PHP Frameworks constantly evolve - their code functionality changes. Class or method is renamed, method has 1 new argument or new class is decoupled.
    <br><br>
    In Symfony world you probably know about <a href="https://symfony.com/doc/current/contributing/code/bc.html">Backward Compatibility Promise</a>.
    It <strong>prevents from unexpected and frustrating BC breaks</strong> and helps users to upgrade gradually thanks to deprecation messages.
    <br><br>
    In this post I will show you <strong>how to work with deprecation messages</strong>.
'''
tweet: "How to deprecate your code without BC breaks? #php #oss"
related_posts: [12, 13]
---

This technique is quite rare to see (apart PHP frameworks). It's very simple to add to your open-source code workflow though - let me convince you.


## Why Write Deprecation Messages?

### If you DON'T

- people will have to find deprecations themselves in the commits - **from programmer to detective**
- you'll **find issues** like "autowire() method missing - what should I do?" at your package on Github
- you'll have to remember, when you upgrade some project using your package few months later    

### If you DO

- people will like you 
- you'll be able to do more BC breaks in your code, because people will know they're taken care off 
- **upgrade of your package will be much easier**
- **machine-readable messages will allow automate upgrades**


To explain last point a bit more: if you write your message in a way, that some parser would be able to understand it, **it would be able to refactor other code accordingly**.

1. Read 

    ```bash
    SomeClass::oldMethod => SomeClass::newMethod
    ```

2. Run

    ```bash
    bin/refactor app src
    ```

3. Enjoy new code! 


That was [the future](https://github.com/tomasvotruba/Rector), now back to the present.


## Today's topic: Changed Method Name 

Let's take real example from real code - a class from [`Nette\Utils` 2.4](https://doc.nette.org/en/2.4/html-elements#toc-elements-content).

What we need to know?

- **a method name has changed**
- from "add" to "addHtml"
- on `Nette\Utils\Html` object  
 
 
**Before** this change you used:

```php
$html = Html::el('div');
$html->add('<strong>I am brand new!</strong>');
```
 
And **after** this change you will use:

```php
$html = Html::el('div');
$html->addHtml('<strong>I am brand new!</strong>');
```


This is the snippet from the `Nette\Util\Html` class we are interested in: 


```php
namespace Nette\Utils;

class Html
{
    public function add(...)
    {
        // ...
    }
}
```

So how to inform all ends users about this?

You can choose from **2 ways to write deprecations messages**, based on your preference.
 

### 1. A `@deprecate` annotation 


```php
namespace Nette\Utils;

class Html
{
    /**
     * @deprecated
     */
    public function add(...)
    {
        $this->addHtml(...);
    }
    
    public function addHtml(...)
    {
        // ...
    }
}
```

This is the least you can do. But you could do better, right?

```php
/**
 * @deprecated Method add() is deprecated.
 */
public function add(...)
```

Should I delete all those methods calls in my code?

```php
/**
 * @deprecated Method add() is deprecated, use addHtml() instead.
 */
public function add(...)
```


**A-ha, that's better!**

<br>

I Have 1 Question for you: **What happens when programmer runs `$html->add(...)` method now?**

...

Well, exactly... **nothing**. **Annotations have no influence on code run**, so it will work and programmer won't notice anything.

<br>

Luckily, there is option that **will actually inform about the deprecation**.



### 2. A `trigger_error()`   

A [`trigger_error()`](http://php.net/manual/en/function.trigger-error.php) is native PHP function, that can inform user about changes in the code. 

With the 2nd argument is level of these messages - there is special constant `E_USER_DEPRECATED` destined for this case.

```php
namespace Nette\Utils;

class Html
{
    public function add(...)
    {
        # we already know how to write useful mesagges
        trigger_error('Method add() is deprecated, use addHtml() instead.', E_USER_DEPRECATED);
        
        $this->addHtml(...);
    })
    
    public function addHtml(...)
    {
        // ...
    }
}
```


You can [see it used in similar way](https://github.com/nette/utils/blob/f1584033b5af945b470533b466b81a789d532034/src/Utils/Html.php#L362) in the original code. 



**What happens when programmer runs `$html->add(...)` method with this type of deprecation?**

2 things:

- The code will run
- **The programmer will be informed**


In case he or she is not ready for upgrade, it can be disabled in application `bootstrap` file:

```php
error_reporting(~E_USER_DEPRECATED);
```

[Source](https://phpfashion.com/jak-spravne-updatovat-nette) (Czech only)



I said...

*It's very simple to add to your open-source code workflow...*

...and this is it!



That was [Symfony's Backward Compatibility Promise](https://symfony.com/doc/current/contributing/code/bc.html) in a nutshell.


Happy coding!
