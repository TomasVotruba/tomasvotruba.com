---
id: 357
title: "How to test Symfony Routes to make Huge&nbsp;Refactoring&nbsp;Safe"
perex: |
    The beauty of [pattern refactoring](/blog/2019/04/15/pattern-refactoring/) with Rector is transforming thousands of elements at once. Like nuclear chain reaction. But to do it safely, we need a high-quality test to ensure the code still works.


    Does a high-quality test mean *a lot of* tests? Not necessarily. Instead of writing many tests to cover all our routes, we can write one smart one. How?


---

I was working on routing refactoring the Symfony 2.8 project a month ago. The test for routing we had only was able to count the routes. That's it.

That's like assuming the nuclear reactor works because there is no fire in the village 5 miles away.

We needed a better test that covers the loading of every route, path, prefix, and everything that `@Route` can hold. This was a rough 10 line idea:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">I&#39;m refactoring configuration ~80 routes from YAML to PHP, but need them to work 😊<br><br>How would you test that all routes all still the same in <a href="https://twitter.com/hashtag/symfony?src=hash&amp;ref_src=twsrc%5Etfw">#symfony</a>?<br><br>This is my go ↓ <a href="https://t.co/hdqLDAEk3v">pic.twitter.com/hdqLDAEk3v</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1519201806819205120?ref_src=twsrc%5Etfw">April 27, 2022</a></blockquote>

<br>

## One Month Later...

We still have this test that is performing great. So far it:

* discovered 5 bugs in pull-request CI checks
* helped us to find 1 dead route

That's why I want to share it with you, step by step. The beauty of it is **you can write this test in 10 minutes and have 0 maintenance cost**. You can use this approach not just for Symfony routing but for loaded entities or anything in the form of an array. The CI feedback that everything still works is priceless.

## 1. Get loaded Routes from Symfony

The first step that we have to do is to get all the loaded routes. For that, we need a `RouterInterface`. We'll look at how to get normalized routed data. Then we'll put everything together in a test case:

```php
/** @var \Symfony\Component\Routing\RouterInterface $router */
$routeCollection = $router->getRouteCollection();

// here we collect route data to an array
$routeMap = [];

foreach ($routeCollection->all() as $name => $route) {
    // we wanted to test only attributes we use, but you can name all of them
    $routeMap[$name] = [
        'path' => $route->getPath(),
        'requirements' => $route->getRequirements(),
        'defaults' => $route->getDefaults(),
        'methods' => $route->getMethods(),
    ];
}

// sort A → Z for nice standardized output
ksort($routeMap);
```

You can try this quickly in a controller or command. `dump($routeMap)` to see if you have collected routes.

<br>

All right! We have the data about routes our project has now. How do we turn it into a future-proof test to avoid nuclear core failure?

## 2. Persist Snapshot to File

The following testing is sometimes called "snapshot", and is very useful for testing HTML outputs. We have data in an `array` with strings. How do we turn it into a snapshot stored in a file?

Well, even more complex test [like Rector I/O](/blog/2020/07/13/the-most-effetive-test-i-found-in-7-years-of-testing) can be turned into snapshots.

We can handle an `array`, right? We'll use the good old "what is an array, is a JSON; what is a JSON, is an array" axiom:

```php
use Nette\Utils\Json;

$routeMapJson = Json::encode($routeMap, Json::PRETTY);
```

<br>

What is a string, can be persisted to file:

```php
file_put_contents(__DIR__ . '/Fixture/expected_route_map.json', $routeMapJson);
```

Now we have a snapshot of our routes **in a single JSON file**. Not just long line, but beautiful indented JSON file. We can open it and go through it ourselves. The routes include prefixes, YAML, XML, annotation, and attribute routes. We don't care about the format, just if everything is in the same place in the end.

If one route name is changed by just one character, we know it. If a new route is added, we know it.

## Easy Maintenance?

If we add a new route, we know it. The test would fail. But that is not what we expect. We added a new route, so we want it there. Oh, we'll have to update the `__DIR__ . '/Fixture/expected_route_map.json'` file.

But how? Is this price for snapshot testing? Do we have to go to the file and manually update it? Do we have to run `file_put_contents()` to update the file contents?

**That's a waste of time we want to use for more meaningful work.**

<br>

Here we'll leverage [testing trick](/blog/2020/07/20/how-to-update-hundreds-of-test-fixtures-with-single-phpunit-run/) I learned from Nikita Popov while I've been contributing php-parser. It's not only a joy to contribute, but you can update tests from a command line. That's right, no work, no manual editing, just `vendor/bin/phpunit` run.

```php
if (getenv('UT')) {
    // update test fixture knowingly, e.g. when new route is added
    FileSystem::write($routeMapJson, __DIR__ . '/Fixture/expected_route_map.json');
}
```

<br>

It could not be simpler. To update the test fixture, add `UT=1` before the PHPUnit run:

```bash
UT=1 vendor/bin/phpunit
```

## The Final `RoutingSnapshotTest`

We have the pieces... now we just put them together:

```php
namespace App\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;

final class RoutingSnapshotTest extends TestCase
{
    public function test(): void
    {
        // create container from Kernel or using KernelTestCase
        $container = $this->createContainer();

        $router = $container->get('router');

        $routeCollection = $router->getRouteCollection();
        $routeMap = $this->createRouteMap($routeCollection);

        $currentRouteMapJson = Json::encode($routeMap, Json::PRETTY);

        $expectedRouteMapFile = __DIR__ . '/Fixture/expected_route_map.json';

        if (getenv('UT')) {
            FileSystem::write($expectedRouteMapFile, $currentRouteMapJson);
        }

        $this->assertJsonStringEqualsJsonFile(
            $expectedRouteMapFile,
            $currentRouteMapJson
        );
    }

    private function createRouteMap(RouteCollection $routeCollection): array
    {
        $routeMap = [];
        foreach ($routeCollection->all() as $name => $route) {
            $routeMap[$name] = [
                'path' => $route->getPath(),
                'requirements' => $route->getRequirements(),
                'defaults' => $route->getDefaults(),
                'methods' => $route->getMethods(),
            ];
        }

        ksort($routeMap);

        return $routeMap;
    }
}
```

<br>

Let's add this test and run it... oh, it fails.
First, we have to generate the fixture file with our little trick:

```bash
UT=1 vendor/bin/phpunit
```

<br>

That's it! Now you're testing all your routes, and you can be sure the nuclear power station is safe.

<br>

Happy coding!

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
