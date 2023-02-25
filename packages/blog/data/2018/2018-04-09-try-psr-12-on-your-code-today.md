---
id: 90
title: "Try PSR-12 on Your Code Today"
perex: |
    The standard is still behind the door, but feedback, before it gets accepted, is very important. After accepting it will be written down and it will be difficult to change anything.


    Try PSR-12 today and see, how it works for your code.
tweet_image: "/assets/images/posts/2018/psr-12/preview.png"


---

## PSR-12 meets ECS

Someone on [Reddit referred a PSR Google Group](https://www.reddit.com/r/PHP/comments/84vafc/phpfig_psr_status_update), where they **asked for real-life PSR-12 ruleset implementation in a coding standard tool**. Korvin Szanto already prepared 1st implementation for PHP CS Fixer, at the moment [only as a commit in](https://github.com/KorvinSzanto/PHP-CS-Fixer/commit/c0b642c186d8f666a64937c2d37442dc77f6f393) the fork.

I put the ruleset to `PSR_12` set in ECS, so you can use it:

```php
// ecs.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::PSR_12);
};
```

## Do You Agree or Disagree with PSR12?

There are still few [missed cases to be covered](https://github.com/KorvinSzanto/PHP-CS-Fixer/milestones), but there is never to soon to get feedback from the community.

<div class="text-center">
    <img src="/assets/images/posts/2018/psr-12/php-cs-fixer-thing.png" alt="PR in PHP CS Fixer?" class="img-thumbnail">
</div>

It will be *a thing*: PSR-12 set is definitely coming to PHP CS Fixer and [PHP_CodeSniffer has also an active issue](https://github.com/squizlabs/PHP_CodeSniffer/issues/750) as well. Both of these tools are more stable, more popular and thus more rigid than ECS. So it will take time before there will be a pull-request and then stable release with PSR-12 set.

**That's an advantage of smaller packages like ECS, they can evolve faster and live in the present.** Only that way ECS 4 already has PSR-12 set on board and ready to use.

### What do I Like?

I like that PSR-12 puts to standard rules that I consider standard for years and most of them are already integrated with ECS [`common` sets](https://github.com/symplify/symplify/tree/master/packages/EasyCodingStandard/config/common):

- it applies PHP 7.1 features, like constant visibility
- concat ` . ` spacing
- mostly spacing
- and letter casing

### What don't I Like?

Symplify code is already checked by PSR-12 ([see pull-request](https://github.com/symplify/symplify/pull/773)):

<div class="text-center">
    <img src="/assets/images/posts/2018/psr-12/symplify-implementation.png" alt="Integration to project with ECS" class="img-thumbnail">
</div>

It was easy to setup and works with 0 changes in the code. But as you can see, there is 1 rule I don't fully agree with.

#### `PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixex`

It takes care of spacing around `!`

```php
if (!$isNotTrue) {
}

if ($isNotTrue) {
}
```

An important code should be visually clear, an unimportant code should not bother us. Personally, **I prefer seeing the negation clearly**, so I know it's a negation:

```php
if (! $isNotTrue) {
}
```

## Try It Yourself Today

Communicate, spread the ideas and find your way. This is only PSR - PS **Recommendation**. It's better to keep things standard for others, [so they can drink water if they're thirsty and not start a research on bottle colors instead](/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/#why-are-standards-so-important). But not a rigid rule that cannot be improved.

<br>

You love it or hate it? Let me know in the comments ↓

<br>

Happy coding!
