---
id: 403
title: "3 Signs Your Project is Becoming Legacy - Mock Rottening"
perex: |
    In [the first post](/blog/3-signs-your-project-is-becoming-legacy-and-how-to-avoid-them), we looked at the long-term effects of our decisions. Turning a legacy project into a fresh one is a matter of the "just do it" approach.

    But there are 3 things we should take with care even if our project seems outside the legacy project category. Second of those are mocks.
---

Imagine you're driving on a highway after long day at work. You think about your day and you look forward to see your spouse and children. Suddenly, you see a police car light flashing right behind you. After few second you realize it's not a drive by and you have to stop by side of the road.

They policeman comes to your car and ask for your driving license, age, status. Then they ask you obligatory quotestion "why do you think you've been stopped?"

You start guessing what it could be:

* "My left pedal is too short?"
* "The right pedal is too long?"
* "My left front light is not having the right power?"
*  "My front right light is only 2700 Kelvin but should be 3000 Kelvin?"
* "My back lights are shining 5 ms later then they should?"

You will check everything that there is on your car and could be one of reasons the policeman stopped you. Even the firmware version of your software that handles temperature inside your car. This could affect your driving abilities if temperature is too high or too low, right? What if there is a recent serious bug in it and you're lacking behind the patch?

After 30 minutes back and forth questions and "No" from policeman side, describing all features of your car, you say "I don't know then".

The policeman will tell you: "I just want to warn you about this highway. The road is frosty in 5 miles ahead and there has been a car crash, so drive safely."

You're filled with mix of frustration, relief and confusion. "I wish they told me sooner, now I'm pretty tired after this 30 minutes of guessing to drive safely."

<br>

## Frustration

And this is exactly how it feels when you're working with mocks. You're checking everything that code has, params, arguments, return types, every param and return type or value for 1st call, for 2nd call... just to find out in the end you only need **one exact result value**.

In our 10-year-legacy upgrades, mocks have been the 2nd most costly pattern that made the upgrade hard.

<br>

## Mock only External services or API

Mocks are great for testing external services, like OpenAI API, Google Maps API, or even your own API that takes credential to setup and also a local database instance. That way we get speed, simplicity and can test our code in isolation:

```php
$openaiResponseMock = $this->createMock(ResponseInterface::class);

$openaiResponseMock->method('getBody')
    ->willReturn('{"choices": [{"text": "Fixed code"}]}');
```

But for the code we own and is located in our `/src`, the mocking does not bring any value.
Mocking is using reflection to fake property values, replace method bodies with made up content and also manipulates return types.

With mocking, we create virtual Matrix that is based on reality, but behind the scenes it's a lie:

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
If we use native PHP, the PHPStan would at least warn us about the type mismatch. But mocks are too much for its power.


## Longterm Benefits

The problem above is typical for any project that is using mocks, and tried to upgrade to PHP 7.0 type declarations.
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

With mocks, this is not possible. **It can take 2-3 months**, as every mocked test have to be gone through manually. Why? The PHPStan cannot read mocks, as the code is artificial and does not reflect the reality. In one line `getName()` can return `string`, in another it can return a `Name` object. It's a mess.

If PHPStan can't read the types of code, Rector can't be used to automate the upgrade.

## How to improve the code?

We're in a mess and we know it. But how to get out of it? The secret is to take it one step at a time, while having the source of truth in CI.

We created a custom PHPStan rule, that makes sure we're reducing useless mocks in our codebase:

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

At fist, it's important to make rule part of CI and passing. So list all the classes that are currently mocked - there could be even 100 allowed types, but don't worry about it now.

Done? Let's merge the PR and move on.

<br>

Next step is to remove one class - the one you find the easiest to remove:

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

Refactor all ocurences of this class being mocked. Make the CI pass and create the PR.
Now you have one less class to mock and you can move on to the next one. It will take sometime, but you'll get there.

<blockquote class="blockquote text-center mt-5 mb-5">
"Do one mock a day and in 3 months, you're done."
</blockquote>

**You'll have a codebase that is easy to upgrade and maintain and also tested.**


## Want to read more about this topic?

* Check out practical write up with examples by Frank de Jonge - [Testing without mocking frameworks](https://blog.frankdejonge.nl/testing-without-mocking-frameworks/)


## PhpSpec to PHPUnit

Are you on Phpspec mocks system? Migrate to PHPUnit very first step - there is [Rector rules set that helps you](https://tomasvotruba.com/blog/2019/03/21/how-to-instantly-migrate-phpspec-to-phpunit). PHPUnit mocking has much better support, even PHPStan extension to help you out a bit.


## One Step at a Time

Give it a try! Take one test at a time and see the value IDE, Rector and PHPStan bring to your project.

<br>

Happy coding!

