---
id: 333
title: "7 Steps to Start with Parallel&nbsp;Run in PHP&nbsp;CLI&nbsp;App"
perex: |
    To be honest, I have no idea what I'm doing. I've read a couple of posts about parallel processes in PHP, but most got me confused even more than before. Too much vague theory links to dozens of open-source packages, 5 alternatives to one operation, and other education faults.
    <br><br>
    What I missed **was a to-do list for a 6-year old PHP programmer**. Straightforward, with everyday terminology developers, already know.
    <br><br>
    Do you want to have **a better idea of how to add a parallel run to one of PHP CLI apps**?<br>
    This post will get you from 0 to padawan in a couple of minutes.

tweet: "New Post on the 🐘 blog: How to Implement Parallel Run in CLI PHP App from a Dummy"
tweet_image: "/assets/images/posts/2021/parallel_dummy.jpg"
---

*Disclaimer: if you do parallel for a couple of years, this post is not for you. This post will only confuse you with incorrect interpretations that you have to correct in tweets and comments. This post is not for experts but for those who want to try it today for the first time.*

<blockquote class="blockquote text-center">
    "If you can't explain it to a 6-year-old,<br>
    you don't understand it yourself."
</blockquote>

Last month I tweeted about [16x faster ECS](/blog/introducing-up-to-16-times-faster-easy-coding-standard/), the most significant performance improvement I've ever seen since upgrade to PHP 7.

<br>

I got one question about the architecture:

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Blog post coming on how you achieved it? It would be good to have blog post on how to do parallel run efficiently in PHP.</p>&mdash; Ishan Vyas (@Ishanvyas22) <a href="https://twitter.com/Ishanvyas22/status/1446085620535758850?ref_src=twsrc%5Etfw">October 7, 2021</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

Today I'll share my limited experience with parallel **CLI PHP Apps**. It's an experience I got by exploring PHPStan code and hundreds of trials and errors. What is CLI PHP App? **A PHP tools that you run in command line** - ECS, php-cs-fixer, PHP_CodeSniffer, PHPStan, Rector, PHPUnit, Composer etc.

Is all clear? Let's start.

<br>

<img src="/assets/images/posts/2021/parallel_dummy.jpg" class="img-thumbnail">

## 1. It's Simpler Then You Think

I met with parallel in [a live stream 4 years ago](https://www.youtube.com/watch?v=ktfyEKUrabw). My first problem with a parallel run was that developers who talked about it made the topic sound very complex. I asked one question to understand one concept better, but in the end, I was even more confused than before I asked.

That made me think:

* *"parallel run in PHP is something very complicated"*
* *"it requires dozens of hours of studying, maybe even studying university courses"*
* *"I need a private paid project that needs this feature, so I have a chance to learn it for a couple of months"*

I have good news for you - none of it is true. You just have to be lucky to come around **sources that make you feel smarter**.

The first point is: **it's simpler than you think**.

## 2. Main Goal? Faster!

We don't implement it because it's cool, not because PHP allows it or not because it improves our architecture.

**We want to get somewhere significantly faster**. We're talking 10-20x faster.


## 3. It's about CPU Threads

Last year, my laptop got a little shower from wild traveling and decided to stop working. Czech law gives the seller a month to process the warranty, so I had to get a replacement for the next month.

I bought the first Lenovo Thinkpad that looked similar to the one I used, so I don't have to learn a new keyboard for a single month. I got a surprise: **the PHPStan run was cut down to half**.

Why? The parallel run is as x-faster, where x is a number of CPU threads. It's not about CPU cores, but **about CPU threads**. In my temporary laptop, there was an [AMD Ryzen](https://en.wikipedia.org/wiki/Ryzen) CPU that had 8 cores but excellent 16 threads.

That means every parallel process based on CPU cores is 16x faster.

Have you waited 2 minutes to finish a command-line process? **Now it's 8 seconds.**

## 4. Look for The Bottle Neck

Typical ECS command looks like this:

```bash
vendor/bin/ecs check src
```

This command finds all PHP files in the `/src` directory and runs foreach to check for coding standard violations. Roughly like this:

```php
$foundFiles = $this->findFiles(__DIR__ . '/src');

foreach ($foundFiles as $foundFile) {
    $this->codingStandardApplication->procesFile($foundFile);
}
```

Before the 2nd file can be processed by coding standard, we have to wait for the 1st file to finish.

**This is the bottleneck.**

How to start with parallelization? Look for "the main" `foreach (...)` in your code.

## 5. Processes are Independent

What do you do when you need a repository service in your project? We inject it via the constructor and use it. It has access to a database, where are data all up-to-date, and we can load, edit and delete them. We trust the stability.

In parallel, this is a bit different. How?

<br>

Imagine you're cooking dinner for your family, and you miss the last 4 ingredients to make the meal tasty. Thank God you have 2 kids! You could send them both to get 4 ingredients, but it would be faster if each of them could get just 2 ingredients.

You're rushing them to get it, and after they leave, you realize they forgot their phones. They can't talk to you, and they can't talk to each other. We don't know when they'll be back or if they found what you need.

**We have to wait till everyone gets back** to see the result.

<br>

How does this story look in PHP code?

```php
// 1. input phase
$neededIngredients = ['onion', 'garlic', 'ajvar', 'chilli'];
$familyMembers = ['son', 'daughter'];

// 2. prepare phase
$familyMembersCount = count($familyMembers);
$ingredientsChunks = array_chunk($neededIngredients, $familyMembersCount)

// 3. run process phase
$foundIngredients = [...];
foreach ($familyMembers as $key => $familyMember) {
    $ingredientsChunk = $ingredientsChunks[$key];
    $foundIngredients[] = $familyMember->findIngredients($ingredientsChunk);
}

return $foundIngredients;
```

<br>

And that's precisely how parallel works in ECS!

* 1 Family member = 1 CPU thread
* 1 needed ingredient = 1 input file
* Ingredients chunk = array of input files for 1 CPU thread

## 6. From Foreach to Command

So now we know the processes run separately, each in its paste. But above we still have foreach. How do we run them separately without waiting for each other?

We refactor services call to another command-line command:

```diff
 foreach ($familyMembers as $key => $familyMember) {
     $ingredientsChunk = $ingredientsChunks[$key];
-    $foundIngredients[] = $familyMember->findIngredients($ingredientsChunk);
+    $foundIngredients[] = exec(
+        'vendor/bin/find-ingredient --member $familyMember --chunk $ingredientsChunk
+     );
 }
```

This way, we create as many subcommands on the background as many family members we have. Each of them runs separately.

<br>

How does this work in ECS? Before, we had one command to process all the files:

```bash
vendor/bin/ecs check /src
```

Now the main command is the same, but it runs itself on the background in multiple threads:

```bash
# this is what we type
vendor/bin/ecs check /src

# this is what really happens
→    vendor/bin/ecs check-worker --cpu-thread 1 --files /src/first.php /src/second.php
→    vendor/bin/ecs check-worker --cpu-thread 2 --files /src/third.php /src/fourth.php
```

What is the `check-worker` command exactly doing? It's the exact copy of the `check` command.
The `check` command used to be `foreach (...)` caller of service, but now it calls standalone processes.

## 7. It's like Calling a Rest API Route

This step was blowing for me. The typical run of ECS checked files for coding standard violations and printed the errors - all inside on PHP container:

```bash
vendor/bin/ecs check /src

Found 25 errors. Fix them with the "--fix" option.
```

But how can we work with nested command calls? We do only have bash there, no PHP, no services, no container. **Like when we call external API:**

```bash
curl /app/find-ingredient --member 1 --chunk onion,garlic
```

Does this remind you of something? What kind of response do we get when we call an API?

```bash
curl /app/find-ingredient --member 1 --chunk onion,garlic
{"onion": "found", "garlic": "not_found"}
```

**A JSON!**

<br>

So when we call the ECS worker command, we expect the JSON:

```bash
→    vendor/bin/ecs check-worker --cpu-thread 1 --files /src/first.php /src/second.php
{"/src/first.php": {"error_count": 0}, "/src/second.php": {"error_count": 3}}
```

This step makes sense to the whole previous workflow. It means we only have to return primary data. We cannot return services, value objects or nested arrays, or metadata. **Only return what you need to show the user.**

<br>

To give you an idea, in ECS, the result for a single file looks like this:

```json
[
    {
        "file_path": "/src/first.php",
        "error_messages": [
            "Use spaces over tabs"
        ],
        "file_diffs": [
            "-$value=1;\n;$value = 1;"
        ]
    }
]
```

## Bonus Tip: Strings? Value Objects to the Confidence

This bonus tip is not limited to parallel, but it's a general lifesaver in an unstable environment.

Seeing arrays and strings above might give you shivers. How can we work with such unreliable data and pass them around our application? I feel you. When I don't have an object in my hand, I feel like I'm naked.

Let's put on pants and use value objects the instant we can:

```php
final class FileResult implements JsonSerializable
{
    public function __construct(
        private string $filePath,
        private array $errorMessages,
        private array $fileDiffs,
    ) {
    }

    // we'll use this method in worker command to send the JSON result
    public function jsonSerialize(): array
    {
        return [
            'file_path' => $this->filePath,
            'error_messages' => $this->errorMessages,
            'file_diffs' => $this->fileDiffs,
        ];
    }
}
```

When the worker command returns a string response, we'll turn it into value objects:

```php
// string
$checkWorkerResult = exec(
    'vendor/bin/ecs check-worker --cpu-thread 1 --files /src/first.php /src/second.php'
);

// json
$checkWorkerJson = Json::decode($checkWorkerResult);

// array of FileResult value objects
$fileResults = [];
foreach ($checkWorkerJson as $fileResultJson) {
    $fileResults[] = new FileResult(
        $fileResultJson['file_path'],
        $fileResultJson['error_messages'],
        $fileResultJson['file_diffs']
    );
}
```

That's it! Give it time, start slowly and make small pull requests.

<br>

Happy coding!
