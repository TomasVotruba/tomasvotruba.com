---
id: 284
title: "The Bullet Proof Symfony&nbsp;Command&nbsp;Naming"
perex: |
    How do you name your Symfony commands? `<Something>Commands` for the class. What about it's console name?
    <br>
    <br>
    If you're like the most people, you don't think about such details that at all.
    But that actually makes [you think twice everytime your create a new command](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock).
    <br>
    <br>
    If you're lazy like me, you have a convention and create one command after another, knowing the naming is based on... wait, let's see.

tweet: "New Post on #php 🐘 blog: The Bullet Proof Symfony Command Naming"
---

Today I was making a new package, that handles 1-click scoping for monorepo packages. I created this command:

```php
use Symfony\Component\Console\Command;

final class GenerateWorkflowCommand extends Command
{
}
```

The hard question: how would you name it?

```bash
bin/console ?
bin/console generate
bin/console generate-workflow
bin/console gen-wof
...
```

## Principle of the Least Surprise

Do you prefer code with many rules and various principles across whole code base, or with 1 clear way to do things? Let's look at existing principles in Symfony ecosystem, that prefer the latter:

<br>

How do you name an action in **Single Action Controller**?

```php
final class PostDetailController extends Controller
{
    public function __invoke(Request $request): Response
    {
    }
}
```

You don't. It's always `__invoke()`, and the class is already named.

<br>

How do you name an event for **Event classes**?

```php
$postAddedEvent = new PostAddedEvent($post);
$this->eventDispatcher->dispatch($postAddedEvent);
```

You don't. It's based on `<event-class>::class`.

<br>

Seeing this. How would name a command?...

<br>

You wouldn't.

*"Wait what?"*

<br>

Since ages, the name is information [coupled to a command class](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock). Imagine that's not the best practise. It's just a practise of habit. What other options we have?

## *Where* do we Name a Command?

```php
use Symfony\Component\Console\Command;

final class GenerateWorkflowCommand extends Command
{
    // here
    protected static $defaultName = '...';

    // or

    // here
    public function configure(): void
    {
        $this->setName('...');
    }
}
```

In pre-historic Symfony versions, you could use also manual name registration in a config file.

<br>

All of these options have 1 problem in common. **When** we create a command → we **have to** think of its name. **If this, then that** (imagine startup for this, right?). Also, we have to add it in the right place - which of those 3 would you pick? We don't care, we don't want to think about that.

Why? **Because we want to think about contents of `execute()` method.** That's the fun part.

## What about Class-based Naming?

```php
final class GenerateWorkflowCommand extends Command { ... }
```

↓

```bash
bin/console generate-workflow
```

<br>

```php
final class DumpStaticSiteCommand extends Command { ... }
```

↓

```bash
bin/console dump-static-site
```

You get the idea. In PHP code, it would look like this:

```php
use Symfony\Component\Console\Command;

final class GenerateWorkflowCommand extends Command
{
    public function configure(): void
    {
        $this->setName('generate-worfklow');
    }
}
```

Pretty clear, right?

*"But wait Tomas! You still need to think about the name. Also, you've just made a typo, lol."*

```diff
-       $this->setName('generate-worfklow');
+       $this->setName('generate-workflow');
```

Well, that happens, when you try to impress your readers.

How could we avoid such awkward situation? If you contribute Symplify or Migrify, you might already know the answer... or if you get inspiration from **the controller and event classes** above.

<br>

*"Use the `<command>::class`!"*

<br>

That's right!

```php
$this->setName(GenerateWorkflowCommand::class);
```

Not like this. With some conversion method:

```php
use Symplify\PackageBuilder\Console\Command\CommandNaming;

$this->setName(CommandNaming::classToName(GenerateWorkflowCommand::class));
```

That's better!

*"Well, now we don't have think about name. But it's much more new code we have to use. Also, you said [static will slowly kill  you](/blog/2020/08/31/how-static-methods-kills-you-like-corona), right?"*

## Handle Command Naming in 1 Place

I don't like it either. It's just another variation of crap code. What about handling the command names in 1 place only? Would that do?

*"Uhm, maybe... what do you mean?"*

```php
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class PackageScoperApplication extends Application
{

    /**
     * Add names to all commands by class-name convention
     * @param Command[] $commands
     */
    public function addCommands(array $commands): void
    {
        // or pass in the constructor
        $commandNaming = new CommandNaming();

        foreach ($commands as $command) {
            $commandName = $commandNaming->resolveFromCommand($command);
            $command->setName($commandName);
        }

        parent::addCommands($commands);
    }
}
```

That's it!

- 1 place to handle command naming <em class="fas fa-fw fa-check text-success fa-lg"></em>
- no more typos <em class="fas fa-fw fa-check text-success fa-lg"></em>
- don't ever think about that <em class="fas fa-fw fa-check text-success fa-lg"></em>

```diff
 use Symfony\Component\Console\Command;

 final class GenerateWorkflowCommand extends Command
 {
     public function configure(): void
     {
-        $this->setName('generate-worfklow');
     }
 }
```

There is one disadvantage though. In case of command rename, the configs that use the command have to updated too <em class="fas fa-fw fa-times text-danger fa-lg"></em>

Is it a good trade off? I don't know.

<br>

**What is your way to not care about command naming?** Let me know in the comments. I'm really curious. It seems like a small irrelevant issues, but if I could choose to have 1 less issue to worry about, I'd take it and focus on something that needs my attention.

<br>

Happy coding!
