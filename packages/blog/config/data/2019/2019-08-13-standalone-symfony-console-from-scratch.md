---
id: 258
title: "Standalone Symfony Console from the Scratch"

perex: "Symfony Console is the one package you will probably use to build a PHP CLI app. It's of one the easiest Symfony components. Why? You **only create Application class, add your Command class and you are ready to go**."

test_slug: SymfonyConsole

tweet: "Post from Community Blog: #Symfony #Console from the Scratch"
---

## Main feature of Symfony Console

This package helps you to create applications like [Composer](https://github.com/composer/composer), [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer), [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) or [Statie](https://github.com/Symplify/Statie), that generates this website.

So in general, to build applications where you:

- need to **access CLI**,
- need to **be fast** - imports, crons, feeds or asynchronous operations
- and don't need any browser rendering.


## 2 Classes to Learn

**Application** - This is the entry point. It contains all commands and routes arguments to them. Something like Application is in Nette or HttpKernel is in Symfony.

**Command** - Handles input data, processes them and return result to the output. Something like Presenter or Controller. One application can have many commands.


### What Belongs to Command?

Before diving into our first command, there is important rule that I want to share with you. In many tutorials you find business logic inside Commands. That is convenient in the begging, but difficult to unlearn later building more commands.

When I wrote *commands is something like Presenter or Controller*, I was talking about *Delegator Pattern*. Like Controller, **it should only delegate arguments to other services and return result to the output**.

This rule will help you to easily avoid:

- using command to run another command
- using command in controller
- using controller in command

Very common, very coupled. You would never use controller inside another controller, right?

*Ok, now you know this. So lets create your first command!*


## Create First Command in 3 Steps

### 1. Install via Composer

```bash
$ composer require symfony/console
```

### 2. Create Console Application

Conventional location is `bin/console` (having `.php` suffix is also fine):
```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Create the Application
$application = new Symfony\Component\Console\Application;

// Run it
$application->run();
```

Now we can run app and see that it's ready:

```bash
$ php bin/console
```

All good?

### 3. Create and Register Command

Let's create command, that will safely hash any password you enter.

```bash
$ composer require nette/security
```

```php
<?php
// src/Command/HashPasswordCommand.php

namespace App\Command;

use Nette\Security\Passwords;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class HashPasswordCommand extends Command
{
    /**
     * In this method setup command, description and its parameters
     */
    protected function configure()
    {
        $this->setName('hash-password');
        $this->setDescription('Hashes provided password with BCRYPT and prints to output.');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password to be hashed.');
    }

    /**
     * Here all logic happens
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $password = $input->getArgument('password');

        $hashedPassword = Passwords::hash($password);

        $output->writeln(sprintf(
            'Your hashed password is: %s', $hashedPassword
        ));

        // return value is important when using CI
        // to fail the build when the command fails
        // 0 = success, other values = fail
        return 0;
    }
}
```

Configure autoloading, add the following to the `composer.json` file:

```json
"autoload": {
        "psr-4": {"App\\": "src/"}
}
```

Dump the autoloader

```bash
$ composer dump-autoload
```

And update our `bin/console` file:

```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Create the Application
$application = new Symfony\Component\Console\Application;

// Register all Commands
$application->add(new App\Command\HashPasswordCommand);

// Run it
$application->run();
```

Now you can run it from CLI with your password as argument:

```bash
$ php bin/console hash-password heslo123
Your hashed password is: $2y$10$NZVuDpvFbqhsBhR1AZZzX.xUHKhr5qtP1qGKjqRM4S9Xakxn1Xgy2
```



## You Are One Step Further

Now you should:

- understand that you need **only 1 class to create simple Command**
- see **Command like a Controller** and should only delegate business logic, not contain it
- know how to **pass argument**, process it and **return result to the output**


### Where to go next?

Still hungry for knowledge? Go check [Symfony documentation](http://symfony.com/doc/current/components/console.html#learn-more) then.

- Do you need to **pass more than 1 value**? E.g. `bin/console hash-password heslo123 --cost=14`? Go check [Command Options](http://symfony.com/doc/current/console/input.html#using-command-options).

- Do you want to **inform user about progress of slow process**? You are looking for [Progress Bar Helper](http://symfony.com/doc/current/components/console/helpers/progressbar.html).

- Do you want to use well-known and pretty Symfony output style (like PHP-CS-Fixer does)? Look at [Console Style Guide](https://symfony.com/blog/new-in-symfony-2-8-console-style-guide).
