---
id: 137
title: "4 Ways to Add Global Option or Argument to Symfony Console Application"
perex: |
    I'm working on [ChangelogLinker](https://github.com/symplify/changeloglinker), a package that makes managing `CHANGELOG.md` very easy - it generates it. It a CLI Application with a [3 Console Commands](https://github.com/Symplify/ChangelogLinker/tree/master/src/Console/Command). All was good, until **I needed to add an argument to all commands at once**... and in lazy, extensible, maintainable way.
tweet: "..."
---

Why? Symplify `CHANGELOG.md` was growing and growing, keeping upgrade data about 3 major versions. Then I realized there can be more `CHANGELOG.md` files, right?

<img src="/assets/images/posts/2018/global-option/multiple-changelog.png" class="img-thumbnail">

At that time, the path to file was hardcoded as `getcwd() . '/CHANGELOG.md'`, so each command worked only with that file:

```bash
vendor/bin/changelog dump-merges
vendor/bin/changelog link
vendor/bin/changelog cleanup
```

But I needed to change the file:

```bash
vendor/bin/changelog dump-merges CHANGELOG.md
vendor/bin/changelog link CHANGELOG-2.md
vendor/bin/changelog cleanup CHANGELOG-3.md
```

We need to add global file argument. So, what option do we have?

## 1. Add Argument to Each Command

```diff
 use Symfony\Component\Console\Command\Command;
 use Symfony\Component\Console\Input\InputArgument;

 final class LinkCommand extends Command
 {
     protected function configure(): void
     {
         // ...
+        $this->addArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
     }
 }


 final class DumpMergesCommandCommand extends Command
 {
     protected function configure(): void
     {
         // ...
+        $this->addArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
     }
 }

 final class LinkCommand extends Command
 {
     protected function configure(): void
     {
         // ...
+        $this->addArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
     }
 }
```

### <em class="fas fa-fw fa-lg fa-check text-success"></em> Advantages

- It's the fastest way - under 2 minutes including reading this post.
- It's the most common way to add argument and options to Commands - most people would understand it.

### <em class="fas fa-fw fa-lg fa-times text-danger"></em> Disadvantages

- Well, have you noticed the "wiht" typo? Now I have to **fix it in every single class**.
- For every change, we have to find and modify every single place this **duplicated code** is in.
- When a new command is added, you have to remember to **add exactly this line there** - you already know [how memory-locks backfire](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/), right?

Good for creating & sell applications, bad for projects you want to work on for a couple of years.

## 2. Modify Application Definition

I'll tell you a secret. There is one place you can **modify definition not just for active command, but for the whole application** - it's Application Definition!

The first simple & short solution you'd [Googled up](https://gist.github.com/dhrrgn/8847309) is to modify it in bin file:

```diff
 <?php

 use Symplify\EasyCodingStandard\Console\Application;
 use Symfony\Component\Console\Input\InputArgument;

 $application = $container->get(Application::class);
+$applicationDefinitoin = $application->getDefinition();
+$applicationDefinitoin->addArguments([
+    new InputArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
+]);
 $application->run();
```

### <em class="fas fa-fw fa-lg fa-check text-success"></em> Advantages

- 1 place to maintain the code

### <em class="fas fa-fw fa-lg fa-times text-danger"></em> Disadvantages

- we program outside the Application - when we get the application somewhere else (e.g. tests), it might break

   ```php
   $application = $container->get(Application::class);
   $application->run();
   // passing `CHANGELOG-2.md` as argument → invalid argument error
   ```

- that's why the should be encapsulated - always prefer tree dependencies over these circle ones

## 3. The <strike>Symfony</strike> Event Subscriber Way

I found this approach on [Matthias Noback's blog](https://matthiasnoback.nl/2013/11/symfony2-add-a-global-option-to-console-commands-and-generate-pid-file/). The process is similar to above, just wrapped in event subscriber that hooks into the Console Application cycle:

```php
<?php

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\ConsoleEvents;

final class FileArgumentEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [ConsoleEvents::COMMAND => 'onConsoleCommand'];
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $applicationDefinition = $event->getCommand()->getApplication()->getDefinition();
        $applicationDefinition->addArguments([
           new InputArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work with')
        ]);
    }
}
```

### <em class="fas fa-fw fa-lg fa-check text-success"></em> Advantages

- there is 1 place to maintain the code
- our application is consistent everywhere

### <em class="fas fa-fw fa-lg fa-times text-danger"></em> Disadvantages

There are now new memory-locks, that not really needed:

- we have to have/add event dispatcher - `composer require symfony/event-dispatcher`
- we have to load it with subscribers/listeners
- we have to pass event dispatcher to symfony console
- we break one of [object calisthenics](https://williamdurand.fr/2013/06/03/object-calisthenics/#5-one-dot-per-line)

Also, would you add routes this way?

```php
class SomeController
{
    public function someAction(Request $request)
    {
        $router = $request->getAttribute('controller')->getContainer()->get('router');
        $router->addRoute('...');
    }
}
```

Above, we ask event to get a service, to invoke a callback on another service. **When you ask event (unique object) for a service (global class), there is something wrong**. Events should work with unique information - they're value objects after all.

<br>

The post is 5 years old and I don't think Matthias still sees this as the best way to go, yet Google shows it in top 5 results. Matthias has a popular and valuable blog (I learned a lot myself back in my early years in Symfony) and some people might think this is the best practice. To add more salt to the wound, this answer also [spread to StackOverflow](https://stackoverflow.com/questions/40674814/how-to-add-a-new-command-line-option-to-symfony-console) without concurrency.

<br>

What is really important? **The definition of application**, nothing more.

## 4. Extend the Application

I'm very happy to see that [composer code has this right](https://github.com/composer/composer/pull/1110/files). There is global option `--working-dir`, that allows you simply run composer in another directory:

```bash
composer update --working-dir projects/open-training

# equals to
cd projects/open-training
composer update
cd ../..
```

How did I find this out? I needed to remove one of the basic options Symfony Console Application has out of the box:

<img src="/assets/images/posts/2018/global-option/basic.png" class="img-thumbnail">

It took me a while but the track lead to [`Application::getDefaultInputDefinition()`](https://github.com/symfony/symfony/blob/59fad59886fc2e47c4e49bcb668a6e1e0795a6d7/src/Symfony/Component/Console/Application.php#L951) method.

```diff
 <?php

 use Symfony\Component\Console\Application;
 use Symfony\Component\Console\Input\InputArgument;

 final class SomeApplication extends Application
 {
+    protected function getDefaultInputDefinition()
+    {
+        $definition = parent::getDefaultInputDefinition();
+        $definition->addArgument(new InputArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work with'));
+
+        return $definition;
+    }
 }
```

`Symfony\Component\Console\Application` is **one of very few classes I'd allow to [extend](https://ocramius.github.io/blog/when-to-declare-classes-final/)**. It's just 1:1 = easy to maintain and change. Not like entity repository, that can have dozens of children.

### <em class="fas fa-fw fa-lg fa-times text-danger"></em> Disadvantages

- very little known → very hard to discover and debug - we should add a note about this to `config.yml`

    ```yaml
    services:
        # we extend default definition here with `file` argument
        SomePackage\Console\SomeApplication: ~
    ```

### <em class="fas fa-fw fa-lg fa-check text-success"></em> Advantages

- **one place**, that creates a consistent code
- **we use API that is designed for these changes**
- you don't need any extra packages, adding anything to the application
- very simple to add: it takes 10 lines
- it just works :)

<br>

For all these reasons, this is the one I prefer. Do you want to see it in the real world? Here [is a commit](https://github.com/Symplify/Symplify/pull/1047/commits/c07c49ae4eff067db7cfe5e5ed1b283ae37c8c29#diff-3b69acbe6b33a88158b373e6e96de097) from Monorepo Builder for our `file` argument.

<br>

## Think in (Anti) Patterns

This post is not just about adding an option/argument to console. It's about applying the best choice in every feature you add. **And if you don't know, look for it**. Don't just blindly take the first result provided Google. It might be popular, widespread, but that doesn't mean it's high quality and valid solution.

Do you see similar anti-patterns as 1, 2 or 3 in your code you're now happy with? How could you make it better?

<br>

Happy coding!
