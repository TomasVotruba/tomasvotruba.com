---
id: 273
title: "4&nbsp;Ways to Make Your&nbsp;Nette&nbsp;Project More&nbsp;Readable"
perex: |
    You can [switch PHP framework you use in a month](/blog/2019/08/26/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours). Yet, **80 % of work lays before the migration** itself, is to take unreliable PHP code structures and **make [it readable](/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases) for developers**.
    <br><br>
    What belongs to these 80 % when it comes to Nette-specific code?
tweet: "New Post on #php 🐘 blog: 4 Ways to Make Your #nettefw Project More Readable"
tweet_image: "/post_images/2020/nette_readability_get_component.gif"
---

Each framework has its documentation. The developers take it and test it in practice, how good the examples stand in real life. Sometimes **the practical experience goes against the documentation primary choice**, like using [dependency injection in Laravel](/blog/2019/03/04/how-to-turn-laravel-from-static-to-dependency-injection-in-one-day).

These practical tips are golden mine, **because they save the company money and developer work time in the further future**.
Do you use Nette? Today, we look at 10 such spots that will bring you code advantage and make changes more comfortable and stable.

## 1. Avoid Array Access

Could you guess, what type is `$something`?

```php
use Nette\Application\UI\Presenter;

final class SomePresenter extends Presenter
{
    public function renderDefault()
    {
        // ...
        $something = $this['user'];
        $something->...?
    }

    // another 300+ lines of code
}
```

What do you think? I'd go with `int` or `User`. It can be a form, a control, a form input... because array access in `Control` or `Presenter` is delegated to...

<img src="/post_images/2020/nette_readability_array_access.png" class="img-thumbnail">

... `getComponent()`/`addComponent()` methods.

### How to Make Such Code Readable?

Add PHPStan that prevent array access on objects:

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoArrayAccessOnObjectRule
```

This little rule might help us to re-introduce an essential yet straightforward coding cue:

- arrays are always arrays and **behave like arrays**
- objects are always objects, and **behave like objects**

Okay, we probably have now over dozens of errors. But what should we do about them?

Use the explicit method, that is hidden in `offsetGet()`/`offsetSet()` methods, e.g:

```diff
-$something = $this['user'];
+$something = $this->getComponent('user');
```

```diff
-$this['user'] = $something ;
+$this->addComponent($something, 'user');
```

###👍

## 2. Be Honest About Components You Use

Alright, now we know its *a* component.

```php
$something = $this->getComponent('user');
$something->...
```

It might look natural since we're used to this syntax. Let's try to forget this habit. How readable is this approach?

Let's look at an example from another well-known area, with a similar context. Would you be able to work with such entity objects?

```php
$entity = $this->getEntity('user');
// here we have only "getId()" method, that is common to all entities
$entity->...

// forget IDE autocomplete for specific entities
$entity->getName();
```

Honestly, [I don't memorize code](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock), my super-smart IDE does it for me.

### How to Make Such Code Readable?

Again, all we need is common sense. Nothing fancy.

How can we get an exact specific entity?

```php
// 1. annotation
/** @var User $user */
$user = $this->getEntity('user');

// 2. add custom method with return type
$user = $this->getUser();

// another way or two you can think of
```

<img src="/post_images/2020/nette_readability_get_component.gif" class="img-thumbnail">


### What are Benefits of Typed Code?

- IDE automated refactoring works on specific components
- IDE can provide autocomplete, that is unique per component
- PHPStan knows the types and component methods and where they're called from
- Rector can refactor components with ease
- the most important: **you know what's going on, even if you're the first day on the project**

<blockquote class="blockquote text-center">
"So many benefits, that sounds great!<br>
But it's a lot of work to get there."
</blockquote>

I feel you, same here. If I were about to do it manually, I would not do it.

Luckily, we **lazy people have tools to work for us**. Let [Rector](https://github.com/rectorphp/rector) handle it:

```php
# rector.php

use Rector\NetteCodeQuality\Rector\Assign\ArrayAccessGetControlToGetComponentMethodCallRector;
use Rector\NetteCodeQuality\Rector\Assign\ArrayAccessSetControlToAddComponentMethodCallRector;
use Rector\NetteCodeQuality\Rector\Assign\MakeGetComponentAssignAnnotatedRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(MakeGetComponentAssignAnnotatedRector::class);
    $services->set(ArrayAccessSetControlToAddComponentMethodCallRector::class);
    $services->set(ArrayAccessGetControlToGetComponentMethodCallRector::class);
};
```

And run Rector:

```bash
vendor/bin/rector process src
```

###👍


## 3. Remove Array Access on Form Controls

The #1 was about use in `Presenter` and `Control`, but what about `Form`?

```php
use Nette\Application\UI\Form;

$form = new Form();
$form->addText('name', 'Name');
```

Different class, same array access magic, same problem.


### How to Make Such Code Readable?

<img src="/post_images/2020/nette_readability_get_form_control.gif" class="img-thumbnail">

This makes code readable in the same points above - for IDE, PHPStan, and Rector.

"So much work?" Rector got you covered:

```php
# rector.php

use Rector\NetteCodeQuality\Rector\ArrayDimFetch\ChangeControlArrayAccessToAnnotatedControlVariableRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ChangeControlArrayAccessToAnnotatedControlVariableRector::class);
};
```

<br>

### Can We do Better?

Imagine you create a form, and somewhere in your code, an input is removed, and select box added. Where? How? Magic!

So even better, **modify forms only in the method they were created**.

Do you need a form that is just slightly different from an already existing one? Don't mutate existing, but rather:

- create **a new form factory**
- decouple common **abstract parent form factory** class for re-use

That way, you promote composition over inheritance in your code and respect SOLID principles.

## 4. Move Latte Engine tuning from Presenter/Control to LatteFactory

Latte modification in the wrong places is more common than you'd expect.

<br>

How would you add `setStrictTypes(true)` for all templates?

```php
use Nette\Application\UI\Presenter;

abstract class AbstractPresenter extends Presenter
{
    public function beforeRender()
    {
        // is this the right place?
        // signals (handle*) are actually called after
        $this->template->getLatte()->setStrictTypes(true);
    }

    // or

    public function templatePrepareFilters()
    {
        // is this the right place?
        // or just for filters?
        $this->template->getLatte()->setStrictTypes(true);
    }
}
```

Ou, don't forget the components. Every single component!

```php
use Nette\Application\UI\Control;

abstract class AbstractControl extends Control
{
    // is this the right place?
    public function __construct()
    {
        // template can be null here
        $this->template->getLatte()->setStrictTypes(true);

        // or

        // isn't this lazy factory? what if the template object is different?
        $this->getTemplate()->getLatte()->setStrictTypes(true);
    }

    // or

    // is this the right place?
    public function render()
    {
        // so now we have to call parent::render() in every child component?
        $this->template->getLatte()->setStrictTypes(true);
    }
}
```

Now we have to enforce this parent control in every other control we have, and we should be fine...

Until we **use templates for mail**. What a surprise when final invoice price was accidentally a string `''` that turned into `0`. Ups, we just got paid 0 € instead of 750 €.

### How to Make Such Code Readable?

Instead of trying to put down every fire our child makes in our home, we could... I don't know, take their matches?

It's also known as *single responsibility principle*. There is **max. 1 place to make 1 change** - meet `LatteFactory`:

```php
<?php

declare(strict_types=1);

namespace App\Latte;

use Latte\Engine;
use Latte\Runtime\FilterInfo;

final class LatteFactory
{
    public function create(): Engine
    {
        $engine = new Engine();
        $engine->setStrictTypes(true);

        return $engine;
    }
}
```

We have a **1 place to modify** `Latte\Engine`. Now we need to tell Nette to use it to create all the templates - with an extension:

```php
<?php

// src/DI/LatteFactoryExtension.php

declare(strict_types=1);

namespace App\DI;

use App\Latte\LatteFactory;
use Latte\Engine;
use Nette\DI\CompilerExtension;

final class LatteFactoryExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $containerBuilder = $this->getContainerBuilder();

        $containerBuilder->addDefinition('app.latteFactory')
            ->setType(LatteFactory::class);

        $latteFactoryDefinition = $containerBuilder->getDefinition('latte.latteFactory');
        $latteFactoryDefinition->setFactory(['@' . LatteFactory::class, 'create']);
        $latteFactoryDefinition->setType(Engine::class);
    }
}
```

Register the extension to config, and you're ready to go:

```yaml
extensions:
    - App\DI\LatteFactoryExtension
```

### Change is the Only Constant

Do we need to add **translator to all templates**?

No need to edit 3 places, just one:

```diff
 <?php

 declare(strict_types=1);

 namespace App\Latte;

 use Latte\Engine;
 use Latte\Runtime\FilterInfo;
+use Nette\Localization\ITranslator;

 final class LatteFactory
 {
+    private ITranslator $translator;
+
+    public function __construct(ITranslator $translator)
+    {
+        $this->translator = $translator;
+    }

     public function create(): Engine
     {
         $engine = new Engine();
         $engine->setStrictTypes(true);

+        $engine->addFilter('translate', function (FilterInfo $filterInfo, ...$args) {
+            return $this->translator->translate(...$args);
+        });

         return $engine;
     }
 }
```

###👍

<br>

That's all for today.

## Removing Magic makes us Feel Safe

Just to repeat the basics. The goal of refactoring is to make code SOLID, unbreakable, and reliable source. When we have a code we understand, we feel safe.

<blockquote class="blockquote text-center mt-5 mb-5">
When we feel safe, we are more productive and make huge changes faster.<br>
If we have to worry about every single line of code, we slowly freeze.
</blockquote>


By 4 steps above, you've just added **5 benefits to your codebase**:

- IDE automated refactoring works on specific components
- IDE can provide autocomplete, that is unique per component
- PHPStan knows the types and component methods and where they're called from
- Rector can refactor components with ease
- the most important: **you know what's going on, even if you're the first day on the project**

<br>

Do you have a tip on how to make Nette code even better? Is there some shortcut I don't know about?

Let me know in the comments ↓. I'd love to learn a new skill that we could apply on Nette projects we upgrade.

<br>

Happy coding!
