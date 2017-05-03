---
layout: post
title: "Combine power of PHP_CodeSniffer and PHP-CS-Fixer in 3 lines"
perex: '''
    PHP_CodeSniffer has over <strong>3 400 stars</strong> on Github and <strong>210 default sniffs</strong>,
    PHP-CS-Fixer with <strong>4 423 stars</strong> brings you <strong>142 fixers</strong> to this day.
    <br><br>
    Both powerful tools dealing with coding standards with <strong>huge communities behind them</strong>.
    Can you imagine using them both and actually enjoy it? Today, I will show you how.
'''
lang: en
---

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/easy-coding-standard-intro/together.png" class="thumbnail">
</div>


### Right to The Answer

Let's say we want to respect [PSR-4 standard](http://www.php-fig.org/psr/psr-4/). 

We add first *checker*, that ensure that class name matches the path:

```yaml
checkers:
    - PhpCsFixer\Fixer\Basic\Psr4Fixer
```

Great start. But what if new programmer puts 2 classes into one file? A **checker above would pass and we have to explain PSR-4 manually in a code review**. Too lazy for that?

So add one more checker:

```yaml
checkers:
    - PhpCsFixer\Fixer\Basic\Psr4Fixer
    - PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneClassPerFileSniff 
```

That's better. **You have just combined PHP_CodeSniffer and PHP-CS-Fixer in 3 lines.**

With a help of [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard). 

@make a gif of checker registration + run 


## How to add EasyCodingStandard in 3 steps   

### 1. Install Package

```bash
composer require symplify/easy-coding-standard:2.0-RC1 squizlabs/php_codesniffer:"3.0.0RC4 as 2.8.1"
```

EasyCodingStandard is built on top of *PHP_CodeSniffer 3*. It release is in RC state since fall 2016 and still not ready, so for now we have to use it. 


### 2. Add `easy-coding-standard.neon` and Configure

Create a `easy-coding-standard.neoon` in your project and configure.
 
```yaml
checkers:
    # PSR-4
    - PhpCsFixer\Fixer\Basic\Psr4Fixer
    - PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneClassPerFileSniff 
```

I recommend you to add a comment to groups, so everyone can easily orientate when there are more checkers added in time.


### Be Lazy with NEON

Do you use PHPStorm? If so, you can use [NEON Plugin](https://plugins.jetbrains.com/plugin/7060-neon-support). It allows you 1 amazing thing:

@gif?

**To fuzzy type classes!**

No more looking to documentation, what string matches what sniff or fixer, if there are any checkers for arrays or debugging typos.

[NEON file format](https://ne-on.org/) is very similar to YAML. 


### 3. Run it!

```bash
vendor/bin/easy-coding-standard check src
```

### 4. Fix it!

```bash
vendor/bin/easy-coding-standard check src --fix
```


That's all!


<!-- Notes for the future: Next series - skip files or two 3rd post - caching -->
