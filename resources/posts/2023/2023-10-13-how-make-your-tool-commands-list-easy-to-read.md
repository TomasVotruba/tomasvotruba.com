---
id: 395
title: "How to make your Tool Commands List Easy to Ready"
perex: |
    Do you use Symfony-console based tools? If you're using Composer, Rector, PHPSpec, PHPStan, ECS or [Class Leak](https://github.com/TomasVotruba/class-leak), you probably know the long list of commands you can use.

    In most of the times, most of command you read are not part of the real package. Do we really need them?
---

The most tools we use daily have a the main command to make them run:

* `vendor/bin/phpstan analyse`
* `vendor/bin/rector process`
* `vendor/bin/ecs check`

<br>

It's easy to use tools, when they work as we expect. When we press the gas in the car and then it increases speed, it's good. But what about unexpected situations like when we press the break and the speed doesn't change?

Sometimes we want to use some other command than the mainstream one - like clearing cache? Listing current setup? Or dumping helper CI script?

<br>

Get ready for a clutter to go through first. Let's eat my own dog food and run Rector commands:

```php
vendor/bin/rector list
````

What we get?

```bash
Available commands:
  completion  Dump the shell completion script
  help        Display help for a command
  list        List commands
  list-rules  Show loaded Rectors
  process     Upgrades or refactors source code with provided rectors
  setup-ci    Add CI workflow to let Rector work for you
  worker      [INTERNAL] Support for parallel process
```

<br>

"Wow, I didn't know Rector has 7 useful commands." you might think. But wait, there is a catch: **4 of those are not useful at all**. They're listed by Symfony Application by default.

## Don't make your Users Think

As a tool user, and as an author of such tools, [I don't want to make you think](https://sensible.com/dont-make-me-think/) and read 7 commands and guess which of those are useless for you.

<br>

Instead, Rector should display only valuable commands:

```bash
Available commands:
 list-rules  Show loaded Rectors
 process     Upgrades or refactors source code with provided rectors
 setup-ci    Add CI workflow to let Rector work for you
```

## How to Change List of Commands?

The solution is simple - **remove the commands you don't want to show**. There is a *hidden* feature in Symfony Command, I've learned just past month. Pun intended.

With this feature, we can remove the commands we don't want to show - right when we're creating the `Symfony\Component\Console\Application` instance.

```php
// somewhere in our container factory

$application = new Symfony\Component\Console\Application();


$commandNamesToHide = ['list', 'completion', 'help', 'worker'];
foreach ($commandNamesToHide as $commandNameToHide) {
    $commandToHide = $application->get($commandNameToHide);
    $commandToHide->setHidden();
}

return $application;
```


The hidden feature is calling a `setHidden()` method on the command instance.

The first three are default Symfony ones, that are useless in CLI tools. The `worker` is a command that is used internally by Rector to run parallel processes.

<br>

There is a popular quote, maybe originated by Robert C. Martin, that says:

<blockquote class="blockquote text-center mt-5 mb-4">
"Code is read ten times more than it's written."
</blockquote>

<br>

In a small tools like ClassLeak, this can mean **narrowing 4 commands down to 1**. Give that a try and save your users time and energy **they can invest in using your tool instead**.

<br>

Happy coding!



