---
id: 349
title: "How to Create Symfony Kernel for Tests with Different Configs"
perex: |
    How can we create 2 tests scenarios for the Symfony Kernel project with 2 different parameters? How can we inject 2 different instances of the same interface? How can we do it in the same way we already configure both of them?
    <br><br>
    Today we'll look at a little trick that allows us to create Symfony Kernel with different configs.

tweet: "New Post on the 🐘 blog: How to Create #Symfony Kernel for Tests with Different Configs"
---

In Symfony, we can use config to define almost any container change - from parameter, a specific implementation of service to extension configuration. That's why it's so easy to use them for tests.

We'll go with the simplest difference - a bool parameter value to make the example simple.

<br>

We want to run tests with 2 different configs:

```yaml
parameters:
    auto_import: true
```

and

```yaml
parameters:
    auto_import: false
```

When we load the first config, the project automatically imports long FQN class names.
If we use the 2nd config, the FQN class names will be untouched. And that's precisely what we want to test!

<br>

What options do we have?

## 1. ENV Variable

The first one that comes to mind is the environment. We have a different environment for development, production, and tests. We can define it in the `phpunit.xml` or directly in the command line before running the test:

```bash
APP_ENV=tests vendor/bin/phpunit
```

<br>

This way, the Symfony project will try to load configs from a specific path, like you can see in [`symfony/demo`](https://github.com/symfony/demo/tree/main/config/packages):

```bash
/config/packages/tests
```

<br>

This way, we can use the environment to load our configs:

```bash
APP_ENV=tests-import-enabled vendor/bin/phpunit
# loads configs from ↓
/config/packages/tests-import-enabled

APP_ENV=tests-import-disabled vendor/bin/phpunit
# loads configs from ↓
/config/packages/tests-import-disabled
```

<br>

As you can see, it's not a very flexible solution.

### Pros and Cons

❌ We'd have to create a new environment for every test

❌ The environment is miss-used as a feature flag

❌ It feels bizarre

❌ We can come across cache issues when we re-run the test with config modifications


## 2. Unit Mocking the Service Configuration

Another option we have is to step away from the Kernel and container completely.
The service we want to test is only a `ClassNameImporter`. Why not use it directly in unit tests?

```php
use PHPUnit\Framework\TestCase;

final class ClassNameImporterTest extends TestCase
{
    public function testImport()
    {
        $classNameImporter = new ClassNameImporter(autoImport: true);
        // ...
    }

    public function testNoImport()
    {
        $classNameImporter = new ClassNameImporter(autoImport: false);
        // ...
    }
}
```

Then we can run tests quickly with expected input and output.

<br>

There is one little problem with false positives. How do we handle `ClassNameImporter` dependencies? We can create our services manually or [mock the external one](/blog/2018/08/30/ways-i-fucked-up-open-source-code-mock-everything-and-test-units/):

```php
$classNameImporter = new ClassNameImporter(
    classNameResolver: new ClassNameResolver(),
    reflectionProvider: $this->getMock(ReflectionProvider::class),
    autoImport: false,
);
```

<br>

### Tested Context !== Real Context

We run the test, it passes, and we merge the pull request. A few hours later, we got a server 500 error reports stream.

* Mocked method in `ReflectionProvider` is removed on actual code
* `ClassNameResolver` dependency was resolved incorrectly by the Symfony kernel container.
* Symfony Kernel could not pass the `$autoImport` parameter because the config used `auto_import`.

These errors *can* be discovered by other tests or static analysis in our CI, but that's wish-full thinking. Instead, we should aim for **standalone robust tests**.

### Pros and Cons

✅ Clear unit approach

✅ Useful for fast bootstrapping of a small project

❌ Rather a puristic approach than pragmatic code

❌ Not flexible for modification - in case of new parameter use or new dependencies, we have to modify the test

❌ Creates technical debt as we have to maintain tests

❌ Does test only minimal part, we miss the framework lifecycle test


## 3. Build Your Custom Dependency Container

Now we know that we need the Symfony dependency container to test interdependencies between our code and the framework.
How can we build it with different configs?

<br>

I wrote about [heavy Symfony Kernel](/blog/when-symfony-http-kernel-is-too-big-hammer-to-use) and how to make your [own light container factory](/blog/introducing-light-kernel-for-symfony-console-apps).

<br>

With this approach, we can load the exact config and fetch the configured service from the DI container:

```php
use PHPUnit\Framework\TestCase;

final class ClassNameImporterTest extends TestCase
{
    public function testImport()
    {
        $containerBuilder = new ContainerBuilder();

        $yamlFileLoader = new YamlFileLoader($containerBuilder);
        $yamlFileLoader->load(__DIR__ . '/config/import_enabled.yaml');

        // compiler passes?
        // bundles?
        // extensions?

        $containerBuilder->compile();
        $classNameImporter = $containerBuilder->get(ClassNameImporter::class);
        // ...
    }

    // ...
}
```

We're getting closer to Symfony lifecycle. But we moved from mocking issues to building Symfony components manually, replacing one problem with another.

### Pros and Cons

✅ The tested context is now much closer to a real-life context

✅ We delegate building dependency container to framework

❌ It's too much code to write and maintain on our own.

❌ We have moved technical debt from our code to framework code maintenance.

❌ It requires deep Symfony container internals knowledge, mainly when it "does not work anymore".

❌ We missed bundles and compiler passes registered in Kernel



## 4. Kernel with Configurable Configs

With this approach, we don't have to learn anything new about Symfony internals, and we can re-use existing methods we already know.

Before we start to enjoy testing with the configurable Kernel, we have to do 2 steps:

### 1. Modify the `AppKernel` to Accept custom Configs on Constructor


```diff
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;

 final class AppKernel extends Kernel
 {
+    /**
+     * @var string[]
+     */
+    private $extraConfigs = [];
-    public function __construct($environment, $debug)
+    /**
+     * @param string[] $configs
+     */
+    public function __construct(string $environment, bool $debug, array $extraConfigs = [])
     {
         parent::__construct($environment, $debug);
+        $this->extraConfigs = $extraConfigs;
     }

     public function registerContainerConfiguration(LoaderInterface $loader)
     {
         // ...
+        foreach ($this->extraConfigs as $extraConfig) {
+            $loader->load($extraConfig);
+        }
     }
 }
```

Now the Kernel can accept an array of configs, and everything else remains in its original shape and untouched.


### 2. Create a test Factory

```php
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;

final class ConfigurableContainerFactory
{
    /**
     * @param string[] $configs
     */
    public function create(array $configs): Container
    {
        // clear cache directory for fresh start
        $filesystem = new Filesystem();
        $filesystem->remove($cacheDirectory);

        $appKernel = new AppKernel('test', true, $configs);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
```

<br>

We've done the hard work. Now we can enjoy a simple test as a reward:

```php
final class ClassNameImporterTest extends TestCase
{
    public function testImports()
    {
        $configurableContainerFactory = new ConfigurableContainerFactory();
        $container = $configurableContainerFactory->create([__DIR__ . '/config/import_enabled.yaml');

        $nameImporter = $container->get(NameImporter::class);

        // ...
    }
}
```

### What about the Performance?

In the case of hundreds of tests like these, we might experience slower tests as a new container is built on every test run. But it's rarely the case when we need to test hundreds of different parameters.

Usually, there is 1 shared container for 95 % of tests, then 5 % test with various combinations of parameters of service modifications.

### Pros and Cons

✅ The tested context is identical to a real-life context

✅ When we upgrade to newer Symfony, there is 0-work with an upgrade as we don't use any of Symfony internals

✅ Verified by time - we've used this approach from Symfony 3 through Symfony 6

✅ Zero test maintenance if our or external dependencies changes

✅ Easy to modify

✅ Easy to add container cache with the same config

✅ Useful for both testing of Symfony apps and Symfony packages

✅ No technical debt

❌ We need to modify the Kernel class

<br>

That's all for today. How do you test your Symfony projects with minimal effort?

<br>

Happy coding!
