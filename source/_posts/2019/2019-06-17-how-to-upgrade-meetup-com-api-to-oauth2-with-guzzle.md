---
id: 213
title: "How to upgrade Meetup.com API to OAuth2 with Guzzle"
perex: |
    I got an email from Meetup.com 5 days ago, that basically every API request will be paid since August 15, 2019. **$ 30/month**, that's like my phone bill.
    <br>
    <br>
     **95 % of data on [Friends Of Php](https://friendsofphp.org/) depend on Meetup.com API** - updated daily. The website is free, so it might kill the content or I'd have to move to crawlers and hope for the lack of protection on Meetup.com against them.
    <br>
    <br>
    **Unless we use Oauth2 before August 15**. I never used it, but how hard that can be, right?
tweet: "New Post on #php 🐘 blog: How to upgrade #Meetupcom API to #OAuth2 with #Guzzle"
---

There is no information about Meetup.com API upgrade on [their blog](https://medium.com/meetup), so I'll share the email to give you an idea:

- API Keys will be replaced by OAuth: We will be removing API keys on August 15, 2019 and requiring you to [authenticate with OAuth](https://www.meetup.com/meetup_api/auth/).

- Move to OAuth soon for continued free API access: Until August 15, members will be able [to apply for OAuth access](https://www.meetup.com/meetup_api/auth/) free of charge. **After August 15, anyone who wants to apply for API access through OAuth will need to have a [Meetup Pro account](https://www.meetup.com/pro/) in order to do so**.

<br>

## Documentation vs. Code

This is a simple task, that in the end has simple 15 lines of new code. But documentation turned it into almost 3 hours of work.

It seems like OAuth2 must be something very new, because Guzzle [supports only Oauth (1)](https://github.com/guzzle/oauth-subscriber). If the last commit in 2014 can be called "supports".

After a bit of Googling if found [kamermans/guzzle-oauth2-subscriber](https://github.com/kamermans/guzzle-oauth2-subscriber). I tried to copy-paste the code

### Choose Your Path

- Do you want to just read copy paste solution? Jump to [5 Steps to Guzzle OAuth2](#5-steps-to-guzzle-oauth2) headline.
- If you want to learn about writing software and documentation, keep on reading.

## Fuck-up #1: Put Oldest First

Why would you put the newest content first, right? You know, like on Twitter, Facebook, basically any news, messages...

<img src="/assets/images/posts/2019/oauth2/old_first.png" class="img-thumbnail">

If you write a code that other people read, you should read [The Design of Everyday Things](
https://www.amazon.com/Design-Everyday-Things-Donald-Norman/dp/1452654123) or [Don't Make Me Think](https://www.amazon.com/gp/product/0321965515/)

So now you can imagine I'm using the latest Guzzle 6 and trying to implement a solution for Guzzle 4 & 5.

<br>

<div class="card">
    <div class="card-body text-center bigger">
        👍 Rule of the thumb: <strong>put news and important information first</strong>.
    </div>
</div>

## Fuck-up #2: Support All Versions

Fuck-Up #1 is a natural consequence of trying to support multiple versions at one branch/tag. Instead of putting all code to one branch, **let always the last branch support the latest LTS dependencies**.

*What does that mean?*

Let's look at [Symfony repository](https://github.com/symfony/symfony). Instead of "Symfony" imagine any package that is version. Now there is Symfony 4, so there was version 3, 2, 1 in the past. Like Guzzle 6, 5, 4...

Now you've decided to upgrade and look for `CHANGELOG.md` (because you haven't heard about [Rector](/blog/2019/02/28/how-to-upgrade-symfony-2-8-to-3-4/) yet):

<img src="/assets/images/posts/2019/oauth2/symfony.png" class="img-thumbnail">

But how do you upgrade from Symfony 3? In the latest branch, there is always **context-aware information**. Do you need any older version? Switch to branch `3.x` or `2.x`.

I love this, **because it focuses on mainstream, providing minimal needed data, but also allows the same for minorities**.

The Symfony docs does the same:

<img src="/assets/images/posts/2019/oauth2/symfony_docs.png" class="img-thumbnail">

I wrote about this in detail in [What can You Learn from Menstruation](/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/) post.

<br>

<div class="card">
    <div class="card-body text-center bigger">
        👍 Rule of the thumb: <strong>never mix 2 major versions in one branch, unless LTS framework</strong>
    </div>
</div>

## Fuck-up #3: Provide more solutions

After I figured out the `README` is not text for programmers but for detectives, I've found the code with middleware I though I was looking for:

```php
<?php

$oauth = new OAuth2Middleware($grant_type);

$stack = HandlerStack::create();
$stack->push($oauth);

$client = new Client([
    'auth'     => 'oauth',
    'handler'  => $stack,
]);
```

This supposes to work... but instead I got as useless exceptions as "cannot authenticate". After 60 minutes of trying, I went to sleep with frustration.

In the morning I've noticed little note:

<img src="/assets/images/posts/2019/oauth2/alter.png" class="img-thumbnail">

Tried it and it worked. WTF? Why there is a broken code first, then "working alternative" second?

This is a common problem with double complexity = exponential bugs. If you translate your website to English and German, it will have more translation bugs than the English-only version. Of course, German might be important to your business, but **the alternative code has as little added value as having a website in American English and British English**.

<div class="card">
    <div class="card-body text-center bigger">
        👍 Rule of the thumb: <strong>don't create alternatives for mainstream, it mostly confuses people</strong>. People will always find alternatives themselves
    </div>
</div>

## How to Write Perfect Documentation?

It's important to know, that I don't try to make this about the specific documentation, but rather about *any* open-source documentation and how it's written.

<img src="/assets/images/posts/2019/oauth2/legacy.png" class="img-thumbnail" style="max-width:35em">

If I'd be sending an invoice to my employer, it would look like this:

- 2,5 hours - debugging documentation
- 0,5 hours - implementing OAuth2

### How to Lower Those 2,5 hours to 15 minutes?

Let's pause a bit and think - **what do we really need, when we use the package for the first time**?

- get a code that solves our problem (e.g. use OAuth2 with Guzzle to prevent paying $ for Meetup.com API)
- solve it as fast as possible (e.g. in 30 minutes)
- a code, that we can copy paste and it works (e.g. without looking for missing use statements - I just hate this!)
- feel confident we understand the errors, so we can fix them ourselves (e.g. without StackOverflow, creating and issue on Github)

<br>

If we agree on these as our priorities, then all we need is working piece of code.

### Be Sure the Code Works without Testing it

**How do we know the code is working?**

- it's tested
- test passing
- we can see test passing ourselves on Travis CI or Gitlab CI

Instead of having documentation with text (= "weak strings"), the best would be:

- link to the code for Guzzle 6
- link to test case for Guzzle 6
- link to CI passing for a test case for Guzzle 6

That's the perfect documentation, **because we can skip the verification and detective part and trust it by validated contract**.

<br>

Now finally to the solution ↓

## 5 Steps to Guzzle Oauth2

- Login to [Meetup.com](http://meetup.com)
- Create new consumer here - https://secure.meetup.com/meetup_api/oauth_consumers - it's *credentials* actually
- There you get Oauth key and secret
- Have the latest `guzzle` + `oauth2-subscriber`

```bash
composer require guzzlehttp/guzzle:^6.3
composer require kamermans/guzzle-oauth2-subscriber
```

- Use Guzzle with Oauth2

```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;

// get these here: https://secure.meetup.com/meetup_api/oauth_consumers
$meetupComOauth2Key = '123';
$meetupComOauth2Secret = 'ABC';

// boilerplate code for Oauth2
$oAuth2Client = new Client([
    // URL for access_token request
    'base_uri' => 'https://secure.meetup.com/oauth2/access',
]);

$oAuth2Config = [
    'client_id' => $meetupComOauth2Key,
    'client_secret' => $meetupComOauth2Secret,
];
$clientCredentials = new ClientCredentials($oAuth2Client, $oauthConfig);
$oAuth2Middleware = new OAuth2Middleware($clientCredentials);


// the main code
$client = new Client();
$client->getConfig('handler')->push($oAuth2Middleware);

// call anything
$response = $client->request('GET', '...');
var_dump($response);
```

But does it still work?

<br>

Happy coding!