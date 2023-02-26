---
id: 258
title: "Standalone Symfony Console from the Scratch"

perex: |
    Symfony Console is *the one* package you will use to build a PHP CLI app. It's one of the easiest Symfony components.


    Why? You **only create Application class, add 1 Command class, and you are ready to go**.


updated_since: "June 2020"
updated_message: |
    Updated with Symfony 5.1 syntax.
---

## When & Why use Symfony Console?

This package helps you to create applications like [Composer](https://github.com/composer/composer), [ECS](https://github.com/symplify/easy-coding-standard), [Rector](https://github.com/rectorphp/rector) or [Symfony Static Dumper](https://github.com/symplify/symfony-static-dumper) that generates this website.

So in general, to build applications where you:

- need to **access CLI**,
- need to **be fast** - imports, CRON jobs, feeds or asynchronous operations
- and don't need any browser rendering.

## 2 Classes to Learn

- **Application** - This is the entry point. It contains all commands and routes arguments to them. Something like Application is in Nette or HttpKernel is in Symfony.
- ** Command** - Handles input data, processes them, and returns the result to the output as the Presenter or Controller does.

1 application can have many commands.

## What Belongs to Command?

Before diving into our first Command, there is an essential rule that I want to share with you. In many tutorials, you find business logic inside Commands. That is convenient in the begging, but challenging to unlearn later, building more commands.

When I wrote *commands is something like Presenter or Controller*, I talked about [Delegator Pattern](/blog/2018/01/08/clean-and-decoupled-controllers-commands-and-event-subscribers-once-and-for-all-with-delegator-pattern/). Like Controller, **it should only delegate arguments to other services and return the result to the output**.

This rule will help you to **avoid**:

- using Command to run another command
- using Command in the Controller
- using Controller in Command

Very common, very coupled. You would never use the Controller inside another controller, right?

*Ok, now you know this. So let's create your first Command!*


## Create First Command in 3 Steps

### 1. Install via Composer

```bash
composer require symfony/console
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

Now we can run the app and see that it's ready:

```bash
php bin/console
```

All good? Good!

### 3. Create and Register Command

Let's create Command that will safely hash any password you enter.

```bash
composer require nette/security
```

```php
<?php

declare(strict_types=1);

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
     * In this method setup command, description, and its parameters
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

        // return value is important when using CI, to fail the build when the command fails
        // in case of fail: "return self::FAILURE;"
        return self::SUCCESS;
    }
}
```

Configure autoloading, add the following to the `composer.json` file:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src"
        }
    }
}
```

Dump the autoloader

```bash
composer dump-autoload
```

And update our `bin/console` file:

```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Create the Application
$application = new Symfony\Component\Console\Application();

// Register all Commands
$application->add(new App\Command\HashPasswordCommand());

// Run it
$application->run();
```

Now you can run it from CLI with your password as an argument:

```bash
php bin/console hash-password heslo123
Your hashed password is: $2y$10$NZVuDpvFbqhsBhR1AZZzX.xUHKhr5qtP1qGKjqRM4S9Xakxn1Xgy2
```

## You Are One Step Further

Now you should:

- understand that you need **only 1 class to create simple Command**
- see **Command like a Controller** and should only delegate business logic, not contain it
- know how to **pass argument**, process it and **return result to the output**

### Where to go next?

Still hungry for knowledge? Check [Symfony documentation](http://symfony.com/doc/current/components/console.html#learn-more) then.

- Do you need to **pass more than 1 value**? E.g. `bin/console hash-password mummy123 --cost=14`? Go check [Command Options](http://symfony.com/doc/current/console/input.html#using-command-options).
- Do you want to **inform users about the progress of slow process**? You are looking for [Progress Bar Helper](http://symfony.com/doc/current/components/console/helpers/progressbar.html).
- Do you want to use a popular and pretty Symfony output style (like PHP-CS-Fixer does)? Look at [Console Style Guide](https://symfony.com/blog/new-in-symfony-2-8-console-style-guide).

<br>

Happy coding!
