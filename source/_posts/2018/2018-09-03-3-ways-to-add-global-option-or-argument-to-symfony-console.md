---
id: 137
title: "3 Ways to Add Global Option or Argument to Symfony Console"
perex: |
    I'm working on [ChangelogLinker](https://github.com/symplify/changeloglinker), a package that makes managing `CHANGELOG.md` very easy - it generates it. It a CLI Application with a [3 Console Commands](https://github.com/Symplify/ChangelogLinker/tree/master/src/Console/Command). All was good, until **I needed to add argument to all commands at once**... and in lazy, extensible, maintainable way.
tweet: "..."
---

Why? Symplify `CHANGELOG.md` was growing and growing, keeping upgrade data about 3 major versions. Then I realized there can be more `CHANGELOG.md` files, right?

<img src="/assets/images/posts/2018/global-option/multiple-changelog.png" class="img-thumbnail">

At that time, the path to file was hardcoded as `getcwd() . '/CHANGELOG.md'`. So each command worked only with that file:

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

What are the options?

## 1. Add Argument to Each Command

```diff
 use Symfony\Component\Console\Command\Command;
 use Symfony\Component\Console\Input\InputArgument;
 
 final class LinkCommand extends Command
 {
     protected function configure(): void
     {
         // ...
         $this->addArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
     }
 }
 
 
 final class DumpMergesCommandCommand extends Command
 {
     protected function configure(): void
     {
         // ...
         $this->addArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
     }
 }
  
 final class LinkCommand extends Command
 {
     protected function configure(): void
     {
         // ...
         $this->addArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
     }
 }
```

:+:

- It's fast: under 2 minutes including reading this post.
- It's the most common way to add argument and options to Commands.

:-:

- Well, have you noticed the `wiht` typo? Now I have to change it to `with` in every single command.
- For any change, you have to find and modify every single place this duplicated code is in.    
- When a new command is added, you have to remember to add exactly this line there - you already know [how memory-locks backfire](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/), right?

Good for create & sell applications, bad for projects you want to work on for couple of years.

### 2. Modify Application Definition

I'll tell you a secret. There is one place you can modify definition not just for active command, but for the whole application.

It's Application Definition! The first simple & short solution I Googled (you'd probably too) is to modify it in bin file:

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

:+:

- 1 place to maintain this messge

:-: 

- breaks encapsulatoin - we program outside the Application = when you get the application somehwere else, ti might broke
   $application = $container->get(Application::class);
   $application->run(); // passing `CHANGELOG-2.md` as argument

([source](https://gist.github.com/dhrrgn/8847309))

### 3. The Event Subscriber way

Another way you'll found on [Matthias Noback's blog](https://matthiasnoback.nl/2013/11/symfony2-add-a-global-option-to-console-commands-and-generate-pid-file/) is using Event Subscriber.
The post is 5 years old and I don't think Matthias still sees this as the best way to go, yet Google shows it in top 5 reseults. When you combine that Matthias has very popular blog with many succesful and valuable posts, some might think this is the best practise to do it in Symfony\Console. *Think before you blink!* 

Unfortutunaly, this answer also spraed to stackoverlofw: https://stackoverflow.com/questions/40674814/how-to-add-a-new-command-line-option-to-symfony-console wihtou concurrency :()

To do similar process than above, just in encapsulated service-based event-subscriber way, you'll have to:

```php
<?php

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
 
final class FileArgumentEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return ['console.command' => 'onConsoleCommand'];
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $applicationDefinitoin = $event->getCommand()->getApplication()->getDefinition();
        $applicationDefinitoin->addArguments([
           new InputArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work wiht');
        ]);
    }
}
```

Tadá!

:+:

- your applicatoin is consistenet everywhere
- you're using symfony evnets and they are cool and hyped among community (though often use in everything than *can* be used, no what *should*)

:-:

- you have to add evnet dispatcre - `composer require symfony/event-dispatcher`
- you have to load it with subscribers/listeners
- you have to pass evnet distapcher to symfony console - another place you might forget

- imagine you'd ge adding routes like

```php
class SomeController
{
    public function someAction(Requiest $request)
    {
        $router = $request->getAttribute('controller')->getContainer()->get('router');
        $router->addRoute('...');
    }
}
```

It just feels wrong, right? And doesn't just feel. This breaks enscapulation - you ask for a router, insice a controller, that reaches out for services locator.

In console code above: you ask for definitino of applicatoin, that you get with command event, that is invoked by event subsribed, that is invoked by event disptacher. That's just mess and people using [claithesnics](https://williamdurand.fr/2013/06/03/object-calisthenics/#5-one-dot-per-line) already know this as it's one of the rules what not to do. 

What is really important? **for definitino of applicatoin**

### 4. The Application itself

WHat is the best way to get to defintion of appliactoin?

I'm happy to see that composer made this right:
https://github.com/composer/composer/pull/1110/files
Morover when I know, how long it took me to find this quite hidden, yet the cleaners option:

```diff
 <?php
 
 use Symfony\Component\Console\Application;

 final class SomeApplication extends Application  
 {
+         protected function getDefaultInputDefinition()
+         {
+             $definition = parent::getDefaultInputDefinition();
+             $definition->addArgument(new InputArgument('file', InputArgument::OPTIONAL, 'Path to changelog file to work with'));
+             return $definition;
+         }
 }
```

And `Symfony\Component\Console\Application` is just one of very few classes I'd allow to extends. It's just 1:1, not like entity repository, that can have many children.

:-:

- very little known, thus very hard to discover - so I reckon adding a note about this somehwere, so people know you're doing it - REDMe, abstract command, or the best  `config.yml`


:+:

- one place, that creats consistent application
- we use api that is designed for htese changes  
- you don't need any extra packages, adding aynthign to application
- very simple to add: it takes 10 lines tops - if you have already extnded appliactoin, even 6 ;) 

it just works :)


So which one do you prefer yourself and why?

Do you see anomally with code in your work you're now happy with? Think of way how to make it betters 


