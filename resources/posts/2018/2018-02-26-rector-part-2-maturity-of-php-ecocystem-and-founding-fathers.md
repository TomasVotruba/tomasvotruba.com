---
id: 78
title: "Rector: Part 2 - Maturity of PHP Ecosystem and Founding Fathers"
perex: |
    What it took for Rector to be born?


    Paradigm shift, ecosystem maturity, need for speed to solve common problems community has. **And a great team you share [your work with](https://austinkleon.com/show-your-work) that feedbacks and reflects.**
---

*Read also:*

- [Part 1 - What and How](/blog/2018/02/19/rector-part-1-what-and-how/)
- [Part 3 - Why Instant Upgrades](/blog/2018/03/05/rector-part-3-why-instant-upgrades/)

<br>

It's not that PHP projects didn't need to be updated until 2017. I surely could delegate hundreds of *upgrade-hours* for my whole career. So **why Now?**

## *Codemods* as Standard

*Codemod* is a tool that modifies your code. And you're ok with it.

**Many years ago [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) was born** by [Greg Sherwood](https://gregsherwood.blogspot.cz/search/label/PHP_CodeSniffer) from Australia. Guess how long ago? [In 2006](https://gregsherwood.blogspot.cz/2006/12/if-not-test-first-then-test-really-soon.html)! Tool that checks your coding standard, tabs and spaces, brackets and quotes. First of it's kind to be mainstream in PHP community.

**It was followed by [PHP CS Fixer](https://github.com/friendsofphp/php-cs-fixer)** with [it's first release in 2014](http://fabien.potencier.org/php-cs-fixer-finally-reaches-version-1-0.html) by [Fabien Potencier](http://fabien.potencier.org). Did you know the first script [had only 106 lines](https://gist.github.com/fabpot/3f25555dce956accd4dd)?

I use daily both of these tools, [they're both awesome and work best together](/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/). Both of them fix the code for you, so you can sleep or have a coffee instead.

It took 12 years and 2 tools with over 4000 stars on Github to get here.

## Is PHP Ready for AST?

A few years ago [Nikita Popov](https://www.npopov.com) started an ambitious project [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser). PHP-Parser parses PHP code to AST. If you're new to Abstract Syntax Tree (AST), check [this post that describes 2 big changes in PHP ecosystem](/blog/2017/11/06/wow-to-change-php-code-with-abstract-syntax-tree/) thanks to AST.

### Both Read & Write?

PHP_CodeSniffer was read only, which is great for letting you know what is wrong, but not much for getting a coffee instead. So fixing part was added.

Same was for PHP-Parser. It can read a code and allow it analysis.
That's what [Ondřej Mirtes](https://ondrej.mirtes.cz) uses in [PHPStan](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/) - context aware PHP analysis ("this variable is of this type").

Again useful, but what about that coffee? It won't make itself.

**I must say, this is breaking point for Rector**. Without this, Rector would be just annoying tool telling you what is wrong and what you should do (and we had enough control already, right?). Super fortunately, [write feature was added and released in 2018](https://github.com/nikic/PHP-Parser/blob/master/doc/component/Pretty_printing.markdown#formatting-preserving-pretty-printing) with php-parser 4.

*Did you know?* That Fabien wanted to use PHP-Parser for PHP CS Fixer in [2012](https://github.com/nikic/PHP-Parser/issues/41), but could not, because the writing part was missing? *Patience makes perfect* - 6 years later it's there.

### Coding Standard + Tool that makes Code Nice and Shiny

PHP AST can be saved, but it still needed a bit polishing:

```diff
 namespace App;

 use Symfony\Component\HttpFoundation\DeprecatedRequest;

 class Controller
 {
-    public function actionIndex(): DeprecatedRequest
+    public function actionIndex(): \Symfony\Component\HttpFoundation\NewRequest
     {
     }
 }
```

There was no tool that could do this for you, until [EasyCodingStandard](https://github.com/symplify/easy-coding-standard) + [`Symplify\CodingStandard`](https://github.com/symplify/coding-standard).

With that combination, just run `vendor/bin/ecs` with [proper setup](https://github.com/symplify/coding-standard#types-should-not-be-referenced-via-a-fullypartially-qualified-name-but-via-a-use-statement) to fix that:

```diff
 namespace App;

+use Symfony\Component\HttpFoundation\NewRequest;
-use Symfony\Component\HttpFoundation\DeprecatedRequest;

 class Controller
 {
-    public function actionIndex(): \Symfony\Component\HttpFoundation\NewRequest
+    public function actionIndex(): NewRequest
     {
     }
 }
```

## Founding Fathers of Rector

That's how [Rector](https://github.com/rectorphp/rector) was born one part after another. At least the ideas.

It's not only about writing code. It's about discussing the idea, finding the right API that not only me but others would understand right away, it's about using other tools. It's about talking with people who already made AST tools, ask for advices and find possible pitfalls. It's about reporting issues and talking with people who maintain  projects you depend on.

### FDD = Friendship-Driven Development

I don't work for at any company so development of Rector doesn't solve my personal issues. That's how most project is born, like PHPStan to check Slevomat's code. That means I needed other motivation - when my frustration of wasted thousands human-hours was not enough.

- **Here I'd like to thank [Petr Vácha](http://petrvacha.com)** for cowork weekend in Brno with in summer 2017, where it all started - in those times named as *Refactor*. You've been great friend for years and courage in times, when I needed it the most.

- **And [David Grudl](https://davidgrudl.com)**, who gave me the motivation to dig deep and "just try it" when I felt desperate and useless always with lightness of Zen master.

- **And also [Nikita Popov](http://nikic.github.com)**, who patiently answered, taught me and fixed all my issues on `nikic/php-parser`.

Without you, I would not make it here today.

<br><br>

Happy coding!
