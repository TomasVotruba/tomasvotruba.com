---
id: 200
title: "How to Upgrade Twig from Underscored to Namespaces"
perex: |
    Symfony [recently announced a new version of Twig](https://symfony.com/blog/new-in-twig-namespaced-classes) with namespaces as we know it. Before PHP 5.2 there was `Underscored_Namespace` - I remember because that was the first version I used.
    <br><br>
    Today I'll show you how to upgrade from `_` to `\\` in few ~~minutes~~ seconds.

tweet: "New Post on #php 🐘 blog: How to Upgrade #twig from Underscored to Namespaces"
tweet_image: "/assets/images/posts/2019/twig-under/twig-image.png"

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
---

<div class="text-center">
    <img src="/assets/images/posts/2019/twig-under/twig-image.png" style="max-width: 28em" >
</div>

<div class="alert alert-sm alert-success mt-3">
    <p>This set would not be possible and as good as it is without you, open-source PHP community.
    I'd like to thank 👏:</p>

    <ul>
        <li><strong><a href="https://github.com/greg0ire">greg0ire</a></strong> <a href="https://github.com/rectorphp/rector/commit/493e418f4691f3a4beadf901bd54ea7406380891">for contributing</a> to this set</li>
        <li>and <strong><a href="https://github.com/enumag">enumag</a></strong> for <a href="https://github.com/rectorphp/rector/search?q=twig+is%3Aissue+author%3Aenumag&amp;unscoped_q=twig+is%3Aissue+author%3Aenumag&amp;type=Issues">battle testing and reported issues</a></li>
    </ul>
</div>

## Find and Replace?

So all we need to do is replace `Twig_` with `Twig\`?

```diff
-Twig_Function
+Twig\Function
```

This would fail since `Twig\Function` class doesn't exist. `Twig\TwigFunction` does. There [150 more cases](https://github.com/rectorphp/rector/blob/a1bd751f14c35e1e22c21ebcc3c26c922b4796a1/config/level/twig/underscore-to-namespace.yaml#L3-L154) where **find and replace fails**.

## 2 Places

We need to replace both docblocks:

```diff
 /**
- * @throws \Twig_Error_Loader
+ * @throws \Twig\Error\LoaderError
  */
 public function render(): void
 {
-    /** @var \Twig_Environment $env */
+    /** @var \Twig\Environment $env */
     $env = $this->getTwigEnv();

     // ...
 }
```

And the code:

```diff
-$safeTwigEnvironment = new \Twig_Environment(
+$safeTwigEnvironment = new \Twig\Environment(
-   new \Twig_Loader_Array([])
+   new \Twig\Loader\ArrayLoader([])
);
```

In a reaction to the Symfony blog post, I see many developers [do upgrades manually](https://github.com/sculpin/sculpin/pull/423/files). In case of 50 changes, it's ok, but private code bases will go 1000+ use cases.

## ~~Code~~ Pattern Refactoring

For [Rector](https://github.com/rectorphp/rector) it just 1 pattern to refactor. Just tell him to process your files `src`:

1. Install Rector

```bash
composer require rector/rector --dev
```

2. Update `rector.php`

```php
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SETS, [
        SetList::TWIG_UNDERSCORE_TO_NAMESPACE,
    ]);
};
```

3. Run Rector

```bash
vendor/bin/rector process src
```

Happy coding!
