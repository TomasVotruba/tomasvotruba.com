---
id: 104
title: "Rectify: Turn All Action Injects to Constructor Injection in Your Symfony Application"
perex: |
    Action Injections are much fun, but it can turn our pretty legacy. How to **refactor out of the legacy back to constructor injection** and still keep that smile on your face?
tweet: "New Post on My Blog: Turn All Action Injections in Your #Symfony Application to Constructor Injection #adr #methodinjection #rector"
tweet_image: "/assets/images/posts/2018/action-injection/everywhere.jpg"
related_items: [94, 88]
---

I wrote about [How to Slowly Turn your Symfony Project to Legacy with Action Injection](/blog/2018/04/23/how-to-slowly-turn-your-symfony-project-to-legacy-with-action-injection) a few weeks ago. It surprised me that **the approach had mostly positive [feedback](/blog/2018/04/23/how-to-slowly-turn-your-symfony-project-to-legacy-with-action-injection/#disqus_thread)**:

<blockquote class="blockquote">
    Couldn't agree more with pretty much everything said! Action injection makes it really confusing on whether an object is treated stateful or stateless (very grey area with the Session for example).
    <footer class="blockquote-footer text-right">Iltar van der Berg</footer>
</blockquote>

<blockquote class="blockquote">
    I'm a Symfony trainer and I'm told to teach people how to use Symfony and talk about this injection pattern. Sob.
    <footer class="blockquote-footer text-right">Alex Rock</footer>
</blockquote>

<blockquote class="blockquote">
    I'm working on a Project that uses action injection pattern and I hate it. I like autowiring but the whole idea about action injection is broken. And this project is in sf28 do we don't use autowiring. Maintainance and development with this pattern is a total nightmare.
    <footer class="blockquote-footer text-right">A</footer>
</blockquote>

<br>

It's natural to **try new patterns with an open heart** and validate them in practice, but **what if** you find this way as not ideal and want to go to constructor injection instead?

How would you change all your 50 controllers with action injections...

```php
<?php declare(strict_types=1);

namespace App\Controller;

final class SomeController
{
    public function detail(int $id, Request $request, ProductRepository $productRepository)
    {
        $this->validateRequest($request);
        $product = $productRepository->find($id);
        // ...
    }
}
```

**...to the constructor injection:**

```php
<?php declare(strict_types=1);

namespace App\Controller;

final class SomeController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function detail(int $id, Request $request)
    {
        $this->validateRequest($request);
        $product = $this->productRepository->find($id);
        // ...
    }
}
```


### How to Waste Week in 1 Team

- 50 controllers, 4 actions per each → 200 services
- some of them are duplicated
- identify services, [`Request` objects](https://symfony.com/doc/current/controller.html#controller-request-argument) and [Argument Resolver objects](https://symfony.com/doc/current/controller/argument_value_resolver.html)
- code-reviews and discussions that might take up-to 5-10 days
- and rebase on new merged PRs... well, you have 4-10 hours of team-work wasted ahead of you.

<br>

**I find the time of my team very precious**, don't you? So I Let Rector do the work.

## 3 Steps to Instant Refactoring of All Controllers

### 1. Install Rector

```bash
composer install rector/rector --dev
```

### 2. Prepare Config

Import the `action-injection-to-constructor-injection` level and configure your Kernel class name.

```yaml
# rector.yml
imports:
    - { resource: 'vendor/rector/rector/config/level/architecture/action-injection-to-constructor-injection.yml' }

parameters:
    kernel_class: 'App\Kernel' # the default value
```

<br>

Do you have `App\Kernel` in your application? Use `--level` in CLI this instead of `rector.yml`:

```bash
vendor/bin/rector ... --level action-injection-to-constructor-injection
```

### 3. Run Rector on Your Code

```bash
vendor/bin/rector process /app --dry-run
```

You should see diffs like:

```diff
 <?php declare(strict_types=1);

 namespace App\Controller;

 final class SomeController
 {
+    /**
+     * @var ProductRepository
+     */
+    private $productRepository;
+
+    public function __construct(ProductRepository $productRepository)
+    {
+        $this->productRepository = $productRepository;
+    }
+
-    public function detail(int $id, Request $request, ProductRepository $productRepository)
+    public function detail(int $id, Request $request)
     {
         $this->validateRequest($request);
-        $product = $productRepository->find($id);
+        $product = $this->productRepository->find($id);
         // ...
     }
 }
```

### 3. Run It

Are all looking good? Run it:

```bash
vendor/bin/rector process /app
```

## Clean Code... Done, but What About Beautiful?

You've probably noticed that code itself is not looking too good. Rector's job is not to clean, but to change the code. It's not a hipster designer, but rather a thermonuclear engineer. **That's why there are coding standards. You can apply your own or if not good enough use Rector's prepared set**:

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs --config vendor/rector/rector/ecs-after-rector.yml --fix
```

And your code is now both **refactored and clean**. That's it!


<br><br>

Happy instant refactoring!
