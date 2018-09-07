---
id: 138
title: "How to Develop Multiple Symfony Applications Fast while Keeping the Quality"
perex: |
    Do you take care of 2 or more projects on the same framework? Do you upgrade them both to the newest version of the framework from time to time?
    Or maybe you're successful, you grow and have 10 such projects.
    <br><br>
    **Today I'll show you how to maintain speed while keeping the maintenance cost low**.
tweet: "New Post on my Blog: How to Develop Multiple #Symfony Applications Fast while Keeping the Quality  #git"
tweet_image: "/assets/images/posts/2018/multi-symfony/replace.png"
---

This idea came originally from tech companies with large code bases and constantly growing products:

<div class="text-center">
    <img src="/assets/images/posts/2018/multi-symfony/intro.png" class="img-thumbnail">
    <br>
    <em>From <a href="https://danluu.com/monorepo/">the first post about monorepo</a> there is</em>
</div>

<br>

Yet I never saw such approach in PHP world. I mean, I saw [monoliths](https://gomonorepo.org/#terms) but never a monorepo. Last year I started to work more closely with Shopsys and they did one crazy thing - [put the sandbox project into monorepo repository](ttps://github.com/shopsys/shopsys/tree/master/project-base). Instead of having 2 repositories - **monorepo and showcase project separated** - like [Symfony](https://github.com/symfony/symfony) and its [Demo](https://github.com/symfony/demo), Laravel, CakePHP, Nette or basically any PHP framework I've seen, ** it is just one**.

I had my concerns about how packages and project development will go together, but now I see it was just fear from unknown.

## I hate Repetitive Work, I Hate Repetitive Work

The fuel for the next step of this approach came last month. I'm currently working on 2 open-sourced Symfony Application (non CLI!) - [open-training](https://github.com/tomasvotruba/open-training) and [open-real-estate](https://github.com/TomasVotruba/open-project).

**How are 2 Symfony applications different**? Well, the entities, repository queries, templates, design and controller actions are unique.
But the PHP, used framework, bundle integration, database type, deploy strategy, own packages that [auto-discover entities](https://github.com/TomasVotruba/open-project/tree/master/packages/auto-discovery) for example, Kernel boilerplate are the same.

One example from last month for all: I made first package that extends EasyAdminBundle (you can [read it here](/blog/2018/08/20/painful-experience-over-solutions-extend-configuration-in-easy-admin-bundle-with-collector/)) for *open-lecture* project. Of course, I need that for *open-real-estate*. Now I had to create a package, make own Github repository, register it on packagist, add it to other project and somehow switch between them for every update... ugh, I guess you would not want to pay me for this bureaucracy.

Since I'm the main investor of myself and I hate wasting time on repetitive work, I decided to give myself a question:

<blockquote class="blockquote text-center">
    "Is possible to maintain 2 Symfony applications in a single repository with ease?"
</blockquote>

Google answered by miss-leading [Multiple Kernels](https://jolicode.com/blog/multiple-applications-with-symfony2) [for one Project](https://stackoverflow.com/questions/45925697/more-than-one-application-per-project-repository-with-symfony-4)..

<br>

The goal is to run...

```bash
*/bin/console server:run
```

...and **have a running website, with own database, own code and also no duplicated code**.

After a few hours of playing with the code (my favorite game), I started to see light in the darkness.
I'm right in the very start of using this architecture, so you'll have the knowledge in the rawest form of fresh experience. It's easier to explain and understand, rather than something I do daily for the last 5 years and don't have to think about it.

## 4 Steps to turn Single-Repository project to Parts of Monorepo

### 1. <strike>App</strike> Unique Namespace

If there are 2 `App\Kernel`, the application would break, because every class has to be unique. Pick a name that is specific for the project - here it's "OpenTrainig" - and rename it in `namespace App\`, `namespace App;`, `use App\` in PHP code. Don't forget the `composer.json` as well.

<div class="text-center">
    <img src="/assets/images/posts/2018/multi-symfony/replace.png" class="img-thumbnail">
    <br>
    <em>PHPStorm → Replace in path</em>
</div>

<br>

And let composer know, what we did:

```bash
composer dump-autoload
```


### 2. Fix Autoload Paths

- `bin/console`

```diff
-require __DIR__ . '/vendor/autoload.php';
+require getcwd() .'/vendor/autoload.php';

// ...
```

- `public/index.php`

This is more complicated because the working directory is no in the monorepo root but in the project root:

```diff
-require __DIR__ . '/../vendor/autoload.php';

+$possibleAutoloadFiles = [
+    // project
+    __DIR__  .'/../vendor/autoload.php',
+    // monorepo
+    __DIR__  .'/../../../vendor/autoload.php',
+];

+foreach ($possibleAutoloadFiles as $possibleAutoloadFile) {
+    if (file_exists($possibleAutoloadFile)) {
+        require $possibleAutoloadFile;
+    }
+}
```

### 3. Bin\Console

Instead of the old root as you're used to...

```bash
bin/console server:run
```

...you have to use projects' `bin/console`:

```bash
projects/open-training/bin/console server:run
projects/open-real-estate/bin/console server:run
```

### 4. Merge Projects Composer

You might be already wondering *How do manage composer dependencies in all this madness?* Well, you have to update projects' `composer.json` separately, pick them all and copy to root `composer.json`. And **be careful** not to use different versions, it might cause the project to work on monorepo but fail after deploying on production...

*Just kidding*, you know I'm too lazy for this

All you need to do is to update projects' `composer.json`. For the rest, there is a tool I use to manage Symplify monorepo dependencies that does the dirty work for you:

```bash
composer require symplify/monorepo-builder

# make sure there is the same version for every package in every composer.json
vendor/bin/monorepo-builder validate

# merge "require", "require-dev", "autoload" and "autoload-dev" from projects to the root composer.json
vendor/bin/monorepo-builder merge

# to make sure we have installed projects' dependencies
composer update 
```

In the root `composer.json` you might want to add some coding standards and static analysis, that is not in projects.
See the [`monorepo-builder.yml` config ](https://github.com/TomasVotruba/open-project/blob/master/monorepo-builder.yml) in my [open-project](https://github.com/TomasVotruba/open-project/) monorepo repository to get the idea how to configure it.

<br>

And that's it! We have now one repository to maintain, no matter how many projects we have, and we met our goals:

- We have the entities, repository queries, templates, design and controller actions **are unique**.
- PHP, used framework, bundle integration, database type, deploy strategy, own packages, Kernel boilerplate **are the same**.

Now we can run application with `projects/open-training/bin/console server:run` and it works!

<br>

Happy maintaining!
