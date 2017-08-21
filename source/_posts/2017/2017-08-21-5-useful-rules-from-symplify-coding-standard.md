---
id: 50
layout: post
title: "5 Useful Rules From Symplify Coding Standard"
perex: '''
     <a href="http://github.com/Symplify/CodingStandard">Symplify Coding Standard</a> was born from Zenify, back from the days I was only Nette programmer. It focuses on <strong>maintainability and clean architecture</strong>. I try to make them simple: <strong>each of them does one job</strong>.  
     <br><br>
     With over 13 000 downloads I think I should write about 5 of them you can use in your projects today. 
'''
---

I wrote about [Object Calisthenics](/blog/2017/06/26/php-object-calisthenics-rules-made-simple-version-3-0-is-out-now/) few weeks ago - they are very strict and not very handy if you're beginner in coding standard worlds.
   
**Symplify Coding standard is complete opposite.** You can start with 1st checker today and your code will be probably able to handle it. It's combination of 23 sniffs and fixers.

The simplest would be... 


### 1. Array property should have default value `[]` to prevent undefined array issues

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/array-default.png" class="img-thumbnail">
</div>

**Use it**

```yaml
# easy-coding-standard.neon
checkers:
  - Symplify\CodingStandard\Fixer\Property/ArrayPropertyDefaultValueFixer
```


### 2. Final Interface 

Once I read [When to declare classes final](https://ocramius.github.io/blog/when-to-declare-classes-final) by [Marco Pivetta](http://ocramius.github.io/) with **tl;dr;**:

*Make your classes always final, if they implement an interface, and no other public methods are defined.*

I was working at [Lekarna.cz](https://www.lekarna.cz/) in that time (finally shipped in the beginning of August, congrats guys!) and we used a lot of interfaces and had lots of code reviews. **So I made a sniff to save us some work.** 

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/final-interface.png" class="img-thumbnail">
</div>

**Use it**

```yaml
# easy-coding-standard.neon
checkers:
    - Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff
```


### 3. Class constant fixer

**Are you on PHP 5.5?** I hope you're [PHP 7.1](/blog/2017/06/05/go-php-71/) already.

Well, since PHP 5.5, you can use `::class` constant instead of string.

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/class-constant.png" class="img-thumbnail">
</div>

**Use it**

```yaml
# easy-coding-standard.neon
checkers:
    -  Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer
```


### 4. Test should be final

This is lighter version of **Final Interface rule**. No brainer. 

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/final-test-case.png" class="img-thumbnail">
</div>

**Use it**

```yaml
# easy-coding-standard.neon
checkers:
    - Symplify\CodingStandard\Sniffs\PHPUnit\FinalTestCaseSniff
```



### 5. Equal Interface

What happens if you implement and interface and add few extra public methods?

**Your IDE autocomplete won't work**, if you don't type hint the class and not the interface.

David Grudl recently wrote about [`$template` methods suggestion in Nette](https://phpfashion.com/phpstorm-a-napovidani-nad-this-template).

This sniff helps you to avoid such cases:

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/complete-implementation.png" class="img-thumbnail">
</div>


**Use it**

```yaml
# easy-coding-standard.neon
checkers:
    - Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff
```


## Experimental Bonus: Refactoring Sniff

`@inject` annotations in Nette have their use cases, but **they are mostly overused and breaking SOLID principles** left and right from my consultancy experience.

Putting annotations back to constructor is quite a work, but this Fixer will help you with that.

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/inject-to-construct.png" class="img-thumbnail">
</div>

**Use it**

```yaml
# easy-coding-standard.neon
checkers:
    -  Symplify\CodingStandard\Fixer\DependencyInjection\InjectToConstructorInjectionFixer
```


### Sold? Try them 


They are used the best with [EasyCodingStandard](/blog/2017/08/07/7-new-features-in-easy-coding-standard-22/): 
 
```bash
composer require --dev symplify/easy-coding-standard symplify/coding-standard
```

Check your code: 

```bash
vendor/bin/ecs check --config vendor/symplify/easy-coding-standard/config/symplify-checkers.neon
```

Fix your code: 

```bash
vendor/bin/ecs check --config vendor/symplify/easy-coding-standard/config/symplify-checkers.neon --fix
```


Let me know how much errors will you find in the comments. I dare you to get to 0! :)


## Rest of the Rules

You can find more rules like Abstract Class, Exception, Trait and Interface naming, indexed array indentation, Controllers with 1 method or invoke and so on in [README](https://github.com/Symplify/CodingStandard).

Happy coding!
