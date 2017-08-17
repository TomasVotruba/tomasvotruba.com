---
id: 50
layout: post
title: "5 Useful Rules From Symplify Coding Standard"
perex: '''
    ...
'''
---

With over 13 000 downloads I should write about them
  
- they were born as time went by and I came the smae issues agian an

https://github.com/Symplify/CodingStandard



...
What Symplify cosign standard?

Rules, very simple in structure, yet powerful in functionality.

I picket 5 of them that you can use projects by themselfes today.





1. Final interface lvl 50
- Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff

- reference final interface @ocramius
- reference final method @ocramius issue


<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/final-interface.png" class="img-thumbnail">
</div>

```yaml
# easy-coding-standard.neon
checkers:
    - Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff
``


2. Class constant fixer
::class references should be used over string for classes and interfaces


<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/class-constant.png" class="img-thumbnail">
</div>


```yaml
# easy-coding-standard.neon
checkers:
    -  Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer
```


3. Equal interface
... Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/complete-implementation.png" class="img-thumbnail">
</div>


use case? nette template a itemplate
... vs ...

once this than that?

David even had to write about ti...

```yaml
# easy-coding-standard.neon
checkers:
    -  Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff
```



4. @inject to cosntructor!!!in Nette

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/inject-to-construct.png" class="img-thumbnail">
</div>

```yaml
# easy-coding-standard.neon
checkers:
    -  Symplify\CodingStandard\Fixer\DependencyInjection\InjectToConstructorInjectionFixer
```


5. Test shoudl be final, phputni

- no brainer

<div>
    <img src="/assets/images/posts/2017/symplify-coding-standard/final-test-case.png" class="img-thumbnail">
</div>


```yaml
# easy-coding-standard.neon
checkers:
    - Symplify\CodingStandard\Sniffs\PHPUnit\FinalTestCaseSniff
```


## Are you Coding Standard Ninja?


You can use them all by 
 
```
composer require --dev symplify/coding-standard
composer require --dev symplify/easy-coding-standard
```


```php
vendor/bin/ecs check --config vendor/symplify/easy-coding-standard/config/symplify-checkers.neon

# and fix like this
vendor/bin/ecs check --config vendor/symplify/easy-coding-standard/config/symplify-checkers.neon --fix
```

Let me know how much errors will you find in the comments. I dare you to get to 0! :)







## Rest of the Rules

@todo: complete specific links

- Architecture
- Naming
    - Abstract class
    - Exception suffix
    - Interfcae suffix
    - Trait suffix
- Structure
    - indexed array
    - new class withotu ()
- Simple PHP rules
    - empty array
    - native method (`__CONSTRUCT`)
- Commenting
    - Property and Constant types
- Controllers method
    - 1 method 
    - __invoke method
- Debug
    - no commented out code
    - no dump() function left-overs