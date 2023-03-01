---
id: 377
title: "How to ask DaVinci and Codex to get the right answer"

perex: |
    Last week, I kicked off the first post about [tips and tricks with GPT](/blog/lets-share-fails-and-tricks-with-gpt). In the meantime, Marcel posted a great practical piece on [GPT and solutions based on exception messages](https://beyondco.de/blog/ai-powered-error-solutions-for-laravel).


    Today, we look into 2 pre-trained models that GPT provides - DaVinci and Codex - and how to talk to them to get what we need.
---

First, let's define what pre-trained model we will talk about:

* **DaVinci** is a text model you know from [Chat GPT](https://chat.openai.com/). You can ask it a question, and it gives us an answer.
* **Codex** is a subset of DaVinci that is tailored for code-related prompts. It can help you fix, improve or complete code. This model is behind [Github Copilot](https://en.wikipedia.org/wiki/GitHub_Copilot#Implementation).

Which one is better for generating unit tests? Intuitively, the second one, right? Let's try it out on a practical example.

## 1. Prepare the prompt script

First, we add a PHP SDK for GPT by Nuno Mauduro to our new project:

```bash
composer require openai-php/client
```

<br>

Then we create a new file, `generate-test.php` and initialize the OpenAI client there:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$client = OpenAI::client('<YOUR_API_KEY>');
```

How simple is that? Oh, where do you get the API key? [Right here](https://beta.openai.com/account/api-keys).

<br>

The last step is to ask the prompt. To keep it simple, we provide the desired model and our prompt:

```php
$result = $client->completions()->create([
    'model' => '<pretrained model>',
    'prompt' => '<our prompt>',
]);
```

<br>

Then we render the response like this:

```php
echo $result['choices'][0]['text'];
```

<br>

Great! Now that we have the bare code, lets **jump to the fun part - prompting** ↓

<br>

## 2. Asking DaVinci

The prompt is a question - it can be a short string like "What is the best way to learn Laravel 10", or in our case - long as the provided PHP code that we want to test. To keep our code clear, we'll add prompt contents to the `prompt.txt` file and load its file contents:

```php
$result = $client->completions()->create([
    'model' => 'text-davinci-003',
    'prompt' => file_get_contents(__DIR__ . '/prompt.txt'),
]);
```

<br>

So, what exactly do we want from the GPT? Generate unit test. **It's better to ask for specific details**. Like testing framework ("PHPUnit), the public method name we want to test ("someMagic"), that we want the data provider, and how many cases it must contain.

<br>

### Rule of thumb: be specific but not overly detailed

Imagine you're talking to a human. The more specific we are, the better the answer will be. But if we start a 5 minutes monolog, about how to write a test, the person will get bored and stop listening. It will be tough for them to see what is essential for us.

Use the same practical language as you would use in a real conversation. **Be specific, but keep it to the point**.

<br>

```
Generate PHPUnit test for a "someMagic()" method with data provider of
4 use cases for this code:

'''php
<?php

class SomeClass
{
    public function someMagic(int $firstNumber, int $secondNumber)
    {
      if ($firstNumber > 10) {
          return $firstNumber * $secondNumber;
      }

      return $firstNumber + $secondNumber;
    }
}
'''
```

<br>

We place this content to `prompt.txt` and run our script:

```php
php generate-test.php
```

<br>

In 2-5 seconds, we should get an answer:

```php
<?php

use PHPUnit\Framework\TestCase;
```

Oh, what is this? It starts as a test case, but some crucial part is missing.

<br>

That's because the GPT answer is **limited by default to 16 tokens**.

We can increase it to more by using [`max_tokens`](https://platform.openai.com/docs/api-reference/completions#completions/create-max_tokens) parameters:

```php
$result = $client->completions()->create([
    'model' => 'text-davinci-003',
    'prompt' => file_get_contents(__DIR__ . '/prompt.txt'),
    'max_tokens' => 1000
]);
```

<br>

The response will be longer now. If we ever get a longer test case that won't fit, we'll increase `max_tokens`.


```bash
php generate-test.php
```

<br>

And voilá, our generated test is here ↓

```php
<?php

use PHPUnit\Framework\TestCase;

class SomeClassTest extends TestCase
{
    /**
     * @covers SomeClass::someMagic
     * @dataProvider someDataProvider
     */
    public function testSomeMagic($firstNumber, $secondNumber, $expected)
    {
        $someClass = new SomeClass();
        $result = $someClass->someMagic($firstNumber, $secondNumber);
        $this->assertEquals($expected, $result);
    }

    public function someDataProvider()
    {
        return [
            [5, 5, 10],
            [12, 5, 60],
            [15, 10, 150],
            [20, 30, 600],
        ];
    }
}
```

At first sight, it looks like a valid PHP code. We can use it as it is!

<br>

Often, the output needs cleaning from comments, fixing text artifacts, adjusting to best practices like `setUp()`, using `yield`, the correct namespace, adding strict types, removing pointless `@covers`, using PHP 8 attributes syntax and other tedious steps that [testgenai.com](https://testgenai.com/) handles for you.


<br>

Let's try the other model now ↓

## 2. Asking Codex

We already have the code ready, so we only change the model:

```php
$result = $client->completions()->create([
    'model' => 'code-davinci-002',
    'prompt' => file_get_contents(__DIR__ . '/prompt.txt'),
    'max_tokens' => 1000
]);
```

<br>

And give it a go:

```bash
php generate-test.php
```

<br>

Voilá the response:

```bash


Data provider:

| firstNumber | secondNumber | retruned value | message          |
| ----------- | :----------- | -------------- | ---------------- |
| 5           | 3            | 3              | less than 10     |
| 5           | 15           | 35             | more than 10     |
| 20          | -5           | -100           | first less than 0 |
| -20         | 150          | 30             | second less than 0 |%
```

<br>

Oh, what is this mess? Does that look like a PHPUnit test? I don't think so!

<br>

What is going on? The prompt is the same, we explained everything, but it seems the `code-davinci-002` model has no clue about what we want. It generates a table (for me) or another mess until the 1000 tokens are used.

### Finetuning the Codex Output

I'm very grateful to [Marcel](https://twitter.com/marcelpociot) for sharing the following trick with me.

<br>

The DaVinci is rather generic and can figure out what we need. Yet when we go more complex, it returns more unstable results.

The Codex, on the contrary, needs more context - *context is everything*, right? We must be even more specific and show **an example of what we want**.

<br>

In practice, we'll hardcode 2 new snippets to the prompt:

* The example input PHP code
* The example output unit test code

```
Generate PHPUnit test for a "someMagic()" method with data provider
of 4 use cases for this code:

'''
<?php

class SomeClass
{
    public function combine($first, $second)
    {
        return $first + $second * 10;
    }
}
'''

Output:

'''
<?php

use PHPUnit\Framework\TestCase

final class SomeTest extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(int $first, int $second, int $expectedResult)
    {
        $someClass = new SomeClass();
        $result = $someClass->combine($first, $second);
        $this->assertSame($expectedResult, $result);
    }
}
'''

Now generate a test for this code:

'''
<?php

class SomeClass
{
    public function someMagic(int $firstNumber, int $secondNumber)
    {
      if ($firstNumber > 10) {
          return $firstNumber * $secondNumber;
      }

      return $firstNumber + $secondNumber;
    }
}
'''

Result:

'''
```

<br>

Imagine we're teaching another human being, and they need an example. What is "generate unit test" mean exactly? Well, precisely this!

<br>

Now, I'll let you run the script for yourself to enjoy the surprise of the result:

```bash
php generate-test.php
```

<br>

Oh, in my case, it generates the test but keeps looping with some markdown and mess until it reaches the 1000 tokens.

<br>

Another tip from Marcel is to use a [`stop` parameter](https://platform.openai.com/docs/api-reference/completions/create#completions/create-stop). This parameter stops the generation when it is reached. We'll get only the first generated test as a result ↓

```php
$result = $client->completions()->create([
    'model' => 'code-davinci-002',
    'prompt' => file_get_contents(__DIR__ . '/prompt.txt'),
    'max_tokens' => 1000,
    'stop' => "'''"
]);
```


<br>

Now re-run and see for yourself:

```bash
php generate-test.php
```



<br>

Play around, discover, and share your experience.

Which one do you prefer? I like the first one at first, but with more complex code, the codex seems more stable. Try [testgenai.com](https://testgenai.com/) to generated test faster on the fly.

<br>

Happy coding!
