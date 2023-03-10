---
id: 376
title: "Lets Share Fails and Tricks with GPT"
perex: |
    Last week, I had many interesting discussions about OpenAI and GPT on [Laracon in Porto](https://laracon.eu/). Especially with [Marcel Pociot](https://twitter.com/marcelpociot).

    I've learned much more in 2 days than on the Internet since December.

    That feels great, and tips seem basic but effective. But as in any other fresh area, finding out about them takes a lot of work. I want to embrace sharing in the GPT community, so here is cherry-pick list of failures and tricks from people **who were generous to share it with me**.
---

<blockquote class="blockquote text-center">
"If you want to go fast, go alone.<br>
If you want to go far, go together".
</blockquote>

## Are ChatGPT and GPT the same thing?

This is where I confused the people I spoke with. I thought GPT was the same as ChatGPT. But it's not. GPT is a model, **ChatGPT is an online service with form** that uses the GPT model.

You can use the ChatGPT from your browser here: https://chat.openai.com.

<img src="/assets/images/posts/2023/chat_gpt.png" class="img-thumbnail mt-3" style="max-width: 30rem">

On the other hand, you can call GPT via REST API. The API is paid service, where you pay for tokens.

<br>

## What is ChatGPT Pro?

It's a premium service of the online form that will run faster. It should cost 42 $.

## How can I start GPT in PHP?

The go-to package in PHP is a [openai-php/client](https://github.com/openai-php/client) created by [Nuno Maduro](https://twitter.com/enunomaduro).

```bash
composer require openai-php/client
```

It's a wrapper around the REST API.

```php
$yourApiKey = getenv('YOUR_API_KEY');
$client = OpenAI::client($yourApiKey);

$result = $client->completions()->create([
    'model' => 'text-davinci-003',
    'prompt' => 'PHP is',
]);

// "an open-source, widely-used, server-side scripting language"
echo $result['choices'][0]['text'];
```

<br>

## How fast is the Response from REST GPT API?

It depends. The shorter the prompt, the faster the response. To give you an idea, the typical [TestGen AI](http://testgenai.com/) **response time is 6-10 seconds**.

Well, unless the GPT is down. Then it takes longer :)

<br>

**Using real-time does not make sense because the response could be faster**.
It's better to send the request to a background queue, let the worker handle it, and show the response when it's done.

I want to refactor TestGen AI to Livewire to address this.

<br>

## Is GPT down, or is my project down?

The day before the conference, TestGen AI stopped working. I didn't know if it was something on my side or if the GPT was down in general.

There is a website that tells you the answer: [status.openai.com](https://status.openai.com/):

<img src="/assets/images/posts/2023/open_ai_status.png" class="img-thumbnail mt-3" style="max-width: 30rem">



## How does Copilot work with Context Files?

This is a bit advanced topic, but it could be helpful, so I put it here. The longer you use the GPT, the more you see that the context is everything.

E.g., let's say we want to ask GPT to generate a unit test for the `ConferenceFactory` class. This class has a dependency in the constructor - a `TalkFactory` and `SpeakerRepository`. To make GPT works the best, you should provide these files too.

This is similar to the way GitHub Copilot works - **it has a context of your project** (not a whole, but some files), and it uses it to generate more tailored code. Here is how [Copilot Internals](https://thakkarparth007.github.io/copilot-explorer/posts/copilot-internals.html).

<br>

In the next post, we'll look at the 2 different models and how to treat them right.

<br>

## Let's share!

* What have you found out about GPT?
* What is a blind way to go?
* How do you use it via REST API?

Let me know in the comments or share on Twitter with <a href="https://twitter.com/search?q=%23gpttips">#GPTtips</a>. I believe we can learn a lot from each other and reach our goals faster.

<br>

Happy coding!




