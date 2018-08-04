---
id: 129
title: "Stylish and Standard Console Output with Symfony Style"
perex: |
    Even if you don't use any component from Symfony or even installed one, you can use this trick in your PHP CLI App.
    <br><br>
    It's simple, provides standard and makes your output look like a design from Apple - useful and nice at the same time.
tweet: "New Post on my Blog: Stylish and Standard Console Output with #Symfony Style"
---

We want to **report various states** in PHP CLI Apps. Success message on the finish, errors message in case of failure or just simple note so users know that command is not stuck but working.

## Too Many Ways to Do 1 Thing

You cna use plain PHP like [in PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/blob/f893189392f9a0566aa837c4bcad3929c60d5348/src/Runner.php#L199):

```php
<?php

try {
    // code
} catch (Exception $exception) {
    echo $exception->getMessage();
    return $exception->getCode();
}
```

There is also a bit advanced use of native `OutputInterface` in command like [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/1c10240da97479274fd40a136c3857ff94f7f93f/src/Console/Command/FixCommand.php#L236-L239):

```php
<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SomeCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...

        $output->write('Working on it!');

        // ...
    }
}
```

The advantage of these approaches is **they cannot be simpler and they're ready to be used**. I bet everyone can use `echo 'DONE';`:

<img src="/assets/images/posts/2018/console-output/plain.png">

The second approach is not as easy, but if you're in a Symfony Command class using PHPStorm all you have to do is hit `Ctrl + Space` on an `$output` variable. And in their time they were good enough.

But we want more than a plain text. **If websites can have CSS, colors, and pictures, why not the CLI output?**

<img src="/assets/images/posts/2018/console-output/console.png">

But it's not about colors, **it's about UX**. <em class="text-white bg-success p-2">Green</em> and <em class="text-white bg-danger p-2">red</em> lines instead of white on black spaghetti like on the first image.

<img src="/assets/images/posts/2018/console-output/tracy.png">

<br>

Last but not least, Symfony `$output` has [few predefined styles](https://symfony.com/doc/current/console/coloring.html#using-color-styles):

```php
<?php

// green text
$output->writeln('<info>foo</info>');

// white text on a red background
$output->writeln('<error>foo</error>');
```

And also some colors and <strong><u>cool stuff</u></strong>:

```php
// green text
$output->writeln('<fg=green>foo</>');

// bold text with underscore
$output->writeln('<options=bold,underscore>foo</>');
```

<br>

Which one do you like so far? So many colors, so many options... maybe too many.

### United We Stand, Divided We Autumn

Do you remember when there were [a dozen ways to create Dependency Injection Container](https://github.com/container-interop/container-interop)? Fortunately, the [PSR-11 was born](https://www.php-fig.org/psr/psr-11/) to solve this and moved our focus to things that matter more.

We don't want to play with colors, with `fg`, `underscore`, `green`, `cyan` (wtf is cyan?) words. Also, you know what they say:

<blockquote class="blockquote text-center">
    Strings?<br>
    Break things.
</blockquote>

**We want to print the error and get back to coding**.

## Symfony 2.8 to the Rescue

I was super happy when the [SymfonyStyle](https://symfony.com/blog/new-in-symfony-2-8-console-style-guide) helper class came with Symfony 2.8. Simple wrapper about all mentioned above, `success()` method, `error()` method, all in API.

<img src="https://farm1.staticflickr.com/666/23555673406_6cbd4f5460_o.png">

I think it's not an understatement to say that `SymfonyStyle` is state of art in this matter.


### 1. It's Easy to Integrate into Symfony Command

PHPStan is [using it](https://github.com/phpstan/phpstan/blob/1e232b3da00671a578b0ba451c5d15c904a82fd5/src/Command/ErrorsConsoleStyle.php#L9):

 ```diff
 <?php

 use Symfony\Component\Console\Command\Command;
 use Symfony\Component\Console\Input\InputInterface;
 use Symfony\Component\Console\Output\OutputInterface;
+use Symfony\Component\Console\Style\SymfonyStyle;

 final class SomeCommand extends Command
 {
     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $output->write('Working on it!');
+         $symfonyStyle = new SymfonyStyle($input, $output);
+         $symfonyStyle->note('Working on it!');
+         $symfonyStyle->success('DONE!');
    }
}
```

### 2. Don't make User Think

When I was 13 years old I've accidentally read [*Don’t Make Me Think*](https://www.sensible.com/dmmt.html), amazing bug about UX, programming and psychology for dummies (I'm about to read 2014-revised version). The main point of the book was the Apple, the UX, and the DX mantra - **create a design that users already expect, don't teach them doing common things differently**.

I recall many CLI Apps that each has different output - no colors, different font-size, cool underlines, error message is not red but success is green etc. **User have to focus on the design and understand it instead of enjoying your app**. WTF of non-red exception is just great!

This class offers a common way not to make use think. ECS users it, Statie uses it, PHPStan uses it, Rector uses and [Steward](https://github.com/lmc-eu/steward/blob/66b90dc1b7325f680481e104ae19f7e6d77e7133/src/Console/Command/Command.php#L52) use it.

### 3. SymfonyStyle as a Service

You can create `SymfonyStyle` in simple static construction as in point 1, but what if you need it somewhere else than in a command? Imagine you have 1200 long Command (~= Controller) and you want to extract logic to another class?

Do you have to pass the whole command there or move the `SymfonyStyle` manually?

Save [the vendor-locking statics](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/) for value objects and enjoy the constructor injection. There are more lines than one because we need to register `Input` and `Output` as a service and autowire their interfaces.

```yaml
services:
    # SymfonyStyle
    Symfony\Component\Console\Input\ArgvInput: ~
    Symfony\Component\Console\Input\InputInterface:
        alias: 'Symfony\Component\Console\Input\ArgvInput'
    Symfony\Component\Console\Output\ConsoleOutput: ~
    Symfony\Component\Console\Output\OutputInterface:
        alias: 'Symfony\Component\Console\Output\ConsoleOutput'
    Symfony\Component\Console\Style\SymfonyStyle: ~
```

```diff
 <?php

 use Symfony\Component\Console\Command\Command;
 use Symfony\Component\Console\Input\InputInterface;
 use Symfony\Component\Console\Output\OutputInterface;
 use Symfony\Component\Console\Style\SymfonyStyle;

 final class SomeCommand extends Command
 {
+    /**
+     * @var SymfonyStyle
+     */
+    private $symfonyStyle;
+
+    public function __construct(SymfonyStyle $symfonyStyle)
+    {
+        $this->symfonyStyle = $symfonyStyle;
+    }
+
     protected function execute(InputInterface $input, OutputInterface $output)
     {
-         $symfonyStyle = new SymfonyStyle($input, $output);
-         $symfonyStyle->note('Working on it!');
+         $this->symfonyStyle->note('Working on it!');
-         $symfonyStyle->success('DONE!');
+         $this->symfonyStyle->success('DONE!');
    }
}
```

### 4. Show Your Style

Last little detail that makes the whole experience nice and smooth. EasyCodingStandard uses SymfonyStyle, but it needed to add 1 extra method.

- There could be 2 services but that extra burden for the contributors... but **we don't make the user think**.
- Then I tried composition, so I had to replicate many public methods. It shoots me back with too much maintenance and duplicated code.
- After seeing these options, I've happily settled with extension:

```php
final class OurStyle extends SymfonyStyle
{
    public function pink(string $message)
    {
        // ...
    }
}
```

*One real example for all, check [`StewardStyle` class on Github](https://github.com/lmc-eu/steward/blob/724f72da6ae732065142f10b4100ea65b7282406/src/Console/Style/StewardStyle.php).*


<br><br>

And that's it!

Try the simple approach today and you'll see you won't regret it:

```php
<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SomeCommand extends Command
{
     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $symfonyStyle = new SymfonyStyle($input, $output);
         $symfonyStyle->note('Working on it!');
         $symfonyStyle->success('DONE!');
    }
}
```


Happy coding!