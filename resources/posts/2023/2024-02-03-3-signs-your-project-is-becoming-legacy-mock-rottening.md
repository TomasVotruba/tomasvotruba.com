---
id: 403
title: "3 Signs Your Project is Becoming Legacy - Mock Rottening"
perex: |
    In [the first post](/blog/3-signs-your-project-is-becoming-legacy-and-how-to-avoid-them), we looked at the long-term effects of our decisions. Turning a legacy project into a fresh one is a matter of the "just do it" approach.

    But there are 3 things we should take with care even if our project seems outside the legacy project category. The second of those is mocks.
---

Imagine you're driving on a highway after a long day at work. You think about your day and look forward to seeing your spouse and children. Suddenly, you see a police car light flashing right behind you. After a few seconds, you realize it's not a drive, and you have to stop by the side of the road.

The policeman comes to your car and asks for your driver's license, age, and status. Then, they ask the obligatory question, " Why do you think you've been stopped?"

You start guessing what it could be:

* "My left pedal is too short?"
* "The right pedal is too long?"
* "My left front light does not have the right power?"
*  "My front right light is only 2700 Kelvin, but should it be 3000 Kelvin?"
* "My back lights are shining 5 ms later than they should?"

You will check everything that could be "the why" the policeman stopped you, even the firmware version of the software that handles the temperature inside your car. If the temperature is too high or too low, this could affect your driving abilities, right? What if there is a severe recent bug in it, and you're lacking behind the patch?

After 30 minutes of back-and-forth questions and "No" from the policeman's side describing your car's features, you say, "I don't know then. "

The policeman will tell you: "I just want to warn you about this highway. The road is frosty 5 miles ahead, and there has been a car crash, so drive safely."

You're filled with a mix of frustration, relief, and confusion. "I wish they had told me sooner. Now I'm pretty tired after this 30 minutes of guessing to drive safely."

<br>

## Frustration

This is precisely how it feels when you're working with mocks. You're checking everything that code has- params, arguments, return types, every param and return type or value for the first call and the second call- to find out that, in the end, you only need one exact result value.

In our 10-year-legacy upgrades, mocks have been the 2nd most costly pattern that made the upgrade hard.

<br>

## Mock only External services or API

Mocks are great for testing external services, like OpenAI API, Google Maps API, or even your own API that takes credentials to set up a local database instance. That way, we get speed and simplicity and can test our code in isolation:

```php
$openaiResponseMock = $this->createMock(ResponseInterface::class);

$openaiResponseMock->method('getBody')
    ->willReturn('{"choices": [{"text": "Fixed code"}]}');
```

But for the code we own that is located in our `/src,` the mocking does not add any value.

Mocking uses reflection to fake property values, replace method bodies with made-up content, and manipulate return types.

With mocking, we create a virtual Matrix that is based on reality, but behind the scenes it's a lie:

```php
class User
{
    /**
     * @return string
     */
    public function getName()
    {
        // ...
    }
}
```

```php
$userMock->method('getName')
    ->willReturn(new Name('Tomas'));
```

Until we add a strict type declaration, the content above is allowed.
If we use native PHP, the PHPStan would at least warn us about the type mismatch. But mocks are too magic to handle.

## Mock External Services, not Value Objects

@todo

E.g. do not mock Request, create it.
do no mock parameter provider, create it


@todo servier provider or data collectors - use them directly as well,
e.g. RequestStack define in setup and use directly


## Longterm Benefits

The problem above is typical for any project using mocks and trying to upgrade to PHP 7.0 type declarations.
Where typical upgrade to PHP 7.0 can take 2-3 days and is a matter of:

```bash
composer require rector/rector --dev
```

↓

```php
# rector.php
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPreparedSets(typeDeclarations: true)
    ->withPhpSets(php70: true);
```

↓

```bash
vendor/bin/rector p
```

With mocks, this is not possible. **It can take 2-3 months**, as every mocked test must be done manually. Why? The PHPStan cannot read mocks, as the code is artificial and does not reflect reality. In one line, `getName()` can return a `string`, in another, it can return a `Name` object. It's a mess.

If PHPStan can't read the types of code, Rector can't be used to automate the upgrade. Saying that, this sounds like a good use case for GPT.

## How to improve the code?

We're in a mess, and we know it. But how to get out of it? The secret is to take it one step at a time while having the source of truth in CI.

We created a custom PHPStan rule that makes sure we're reducing useless mocks in our codebase:

```bash
composer require symplify/phpstan-rules --dev
```

Register rule in `phpstan.neon` and list all classes that are allowed to be mocked:

```yaml
# phpstan.neon
services:
    -
        class: Symplify\PHPStanRules\Rules\PHPUnit\NoTestMocksRule
        tags: [phpstan.rules.rule]
        arguments:
            allowedTypes:
                - "Aws\\S3\\S3Client"
                - "App\\Pricing\\PriceManager"
```

At first, making the rule part of CI and passing is essential. So list all the currently mocked classes - there could be even 100 allowed types, but don't worry about it now.

Done? Let's merge the PR and move on.

<br>

The next step is to remove one class - the one you find the easiest to remove:

```diff
 services:
     -
         class: Symplify\PHPStanRules\Rules\PHPUnit\NoTestMocksRule
         tags: [phpstan.rules.rule]
         arguments:
             allowedTypes:
                 - "Aws\\S3\\S3Client"
-                - "App\\Pricing\\PriceManager"
```

Refactor all occurrences of this class being mocked. Make the CI pass and create the PR.
Now, you have one less class to mock, and you can move on to the next one. It will take some time, but you'll get there.

<blockquote class="blockquote text-center mt-5 mb-5">
"Do one mock a day, and in 3 months, you're done."
</blockquote>

**You'll have a codebase that is easy to upgrade, maintain, and test.**


## Want to read more about this topic?

* Check out a practical write-up with examples by Frank de Jonge - [Testing without mocking frameworks](https://blog.frankdejonge.nl/testing-without-mocking-frameworks/)


## PhpSpec to PHPUnit

Are you on the Phpspec mocks system? The first step is to migrate to PHPUnit. There is a [Rector rules set that helps you](https://tomasvotruba.com/blog/2019/03/21/how-to-instantly-migrate-phpspec-to-phpunit). PHPUnit mocking has much better support; even the [PHPStan PHPUnit extension](https://github.com/phpstan/phpstan-phpunit) can help you.


## One Step at a Time

Give it a try! Take one test at a time and see the value IDE, Rector, and PHPStan bring to your project.

<br>

Happy coding!

