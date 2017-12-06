---
id: 53
title: "How ApiGen Survived its Own Death"
perex: '''
    <a href="https://github.com/apigen/apigen">ApiGen</a> was broken for a long time. It depended on Reflection package, that was not developed since 2013 and was unable to parse <em>newer</em> code. When I say newer, I mean <em>hot</em> PHP features like <code>::class</code> in 5.5. I don't even talk about 5.6 or 7.
    <br><br>
    I got frustrated. I spent a year on a project that is still not working out of the box. So I took spring off to change it. <strong>My goal was to replace reflection or let the project die in peace</strong>.
    <br><br>
    This is story about the whole journey of ups and downs.
'''
tweet: "How ApiGen Survived its Death #php #apigen #phpdoc #teamwork"
tweet_image: "assets/images/posts/2017/apigen-revival/apigen.png"
---

Prepare for deep darkness, (almost) burning out and... team work that helped me to make it to the end.

<img src="/assets/images/posts/2017/apigen-revival/apigen.png" class="img-thumbnail">


## Step 1: Bump to PHP 7.1

I love PHP 7.1 and I use it everywhere since its release in 2016. This year [more and more big projects are migrating](/blog/2017/06/05/go-php-71/), yet this is still low % of all packages.

So my condition was to [bump minimal requirement to PHP 7.1](https://github.com/ApiGen/ApiGen/issues/779#issuecomment-285960383). Current maintainers agreed, so I had green on. Thank you [Alexander Jank](https://github.com/jankal) for your support in my first PRs to ApiGen this year. 


## Step 2: Pick the right Reflection Successor

ApiGen uses reflection to analyze classes, their methods, interfaces, traits, parent class of the class, their methods etc.

**Pure PHP reflection can be barely used for advanced tasks like the last one**, so I'd have to rewrite whole package myself. That's not a way to go if you want to have a calm life and normal sleep.

The question was, **where to find the right one?**

I knew a few, but not any that would be able to parse PHP 7.1 by itself. I was aware of [nikic/php-parser](https://github.com/nikic/PHP-Parser), that was maintained and future-compatible (Nikic even added PHP 7.2 features recently). But it was only parsing tool, not smart reflection wrapper.


### Reaching out for Help

In that time, I came across [Projects using the PHP Parser](https://github.com/nikic/PHP-Parser/wiki/Projects-using-the-PHP-Parser) on Wiki of PHP-Parse, I consulted with [Jan Tvrdik](https://github.com/jantvrdik) and [Ondrej Mirtes](https://twitter.com/OndrejMirtes). 

This all [led me to a package](https://github.com/ApiGen/ApiGen/issues/817) called [Roave/BetterReflection](https://github.com/roave/better-reflection) by [James Titcumb](https://www.jamestitcumb.com/) and [Marco Pivetta](https://ocramius.github.io/).
 
I'm bit suspicious to projects that were lastly tagged a half year ago, but I felt I could gave it a go.


## Step 3: Replace Reflection

All right, package picked! The <strike>fun</strike> hell was about to begin.

Imagine your whole application uses everywhere a package, that got stuck 4 PHP versions ago. You feel it more and more. Everyday you need to patch it because there are other packages that are up-to-date. You know, like PHP itself.

When I maintained the ApiGen in 2014, I felt I should [interface everything](https://ocramius.github.io/blog/when-to-declare-classes-final/) - thank you for that *past me*. All reflection classes were interfaced, and there was somewhat of a bridge between TokenReflection (the old package) and ApiGen value objects for Reflections.

Still, I could not drop all old reflections without having prepared all new ones.

But the algorithm alone was simple (well after stepping back from ~3 k lines of code): 

- 1. a 3rd party package's `<class>Reflection` goes in
- 2. ApiGen transforms it 
- 3. an ApiGen Reflection comes out


### Collector + Transformer + Router to the rescue

I don't know how that happened, but I managed to combine 3 patterns to make this work.

By "making this work" I mean:
 
- **keep old** reflection package as stable fallback
- **use new reflection package** only on single reflection class, e.g. `ClassConstantReflection`

The main service that takes cares of this is [TransformerCollector](https://github.com/ApiGen/ApiGen/blob/a6f56691d87f74a64b31a15b7866d5a839aecb60/packages/Reflection/src/TransformerCollector.php#).

All particular Transformers are **collected** into it.

When reflection is passed, ApiGen will decide what to do with it - that is [the Router part](https://github.com/ApiGen/ApiGen/blob/a6f56691d87f74a64b31a15b7866d5a839aecb60/packages/Reflection/src/TransformerCollector.php#L68-L71).

Having this setup ApiGen could:

- 1. have old TokenReflection
- 2. match specific classes and reparse them with BetterReflection
- 3. output ApiGen Reflection value objects 

In time, I've added more and more BetterReflection transformers, like [ClassReflectionTransformer](https://github.com/ApiGen/ApiGen/blob/a6f56691d87f74a64b31a15b7866d5a839aecb60/packages/Reflection/src/Transformer/BetterReflection/Class_/ClassReflectionTransformer.php#L46) that handles ClassReflection.

*[10 PRs later...](https://github.com/ApiGen/ApiGen/issues/817)*

There was light in the of the tunnel.

**Rule of a thumb: when you need to replace something, build a bridge/router and do it gradually. You'll be both safe and in progress.**

**Never rewrite running project from scratch**, unless you really have to.


## Step 4: Is Ship Going Down?

I had dilemma about global constants that BetterReflection doesn't support, yet TokenReflection does. I didn't know what to do with that and I was stuck.

Then, Jan Tvrdik and I have a chat at one grill party about this. Jan helped me to realize, **that single issue should not stand in a way of saving the project**. I could drop it and if anyone needs it, he or she might implement it. That was a huge step forward for me and the project.


### Drop Everything You don't Need - Just to Breathe

I also came to the state, when there was too much coupling of **features I didn't feel like they were useful anymore**.

Using Markdown in docblock descriptions to highlight them was one of them. I never saw that in any code except ApiGen and I still don't think using Markdown in PHP code is a good idea.

I proposed issue [of turning Markdown down](https://github.com/ApiGen/ApiGen/issues/782#issuecomment-285988252) and adding event instead, so anyone could implement if really needed and [it solved around 10 issues](https://github.com/ApiGen/ApiGen/pull/795). Just like that.
 
**Rule of a thumb? When you're stuck in open-source project, drop things that are the least relevant to the project.** It might give you space to breathe and to move forward to next release.


## Step 5. Reflection Replaced

*10 more PRs later*

Reflection works - PHP 7.1 code is perfectly parsed with no issues!
 
<img src="/assets/images/posts/2017/apigen-revival/done.png" class="img-thumbnail">

It didn't have cool features like tree references, but I was able to parse PHP 5.5, PHP 5.6, PHP 7.0 and PHP 7.1 with no problem at all. We could finally close over 30 opened issues that spread for last 4 years.

This gave me a dopamine shot, yet worsts was about to come. 


## Step 6. Burnout 

It was June 2017. The main issue was solved and there was plenty space to improve ApiGen. **But not motivation**. I didn't use it personally and I felt I was there mainly to replace reflection, because I was the one was responsible for its design.

After I finished that, I was looking for co-maintainer and somebody who's using the project to took over me.

Personally, I felt <strike>bit</strike> burned out. What now? Give up the project? Go to cinema? Go to a coach? ✓

What helped me was a [release-candidate](https://github.com/dbrock/semver-howto#release-candidates-100-foo). An illusion of milestone, that closes huge part of dev work.

I released [ApiGen 5.0-RC1](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC1) on June 3rd 2017. It **actually brought more attention then I expected**. Release Candidates are apparently sign that project is back to life.



## Step 7. Saved!

Situation changed with [5.0-RC3](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC3).

<img src="/assets/images/posts/2017/apigen-revival/rc3.png" class="img-thumbnail">

As you can see, [Vlasta Vesely](https://github.com/vlastavesely) did almost 90 % of work on the release. I went to Brno to meet Vlasta, we had great chat and wine - thank you for both. 

**I asked Vlasta to join ApiGen maintainer team and he agreed**. The future is bright now. 


And that is the whole journey up to present moment.


### What would be the main takeway?
 
It's all about team work, priorities, communication of miss-understandings and dealing with your own shit you find on the way home.

Now to the shorter practical part...

## What changed and what was removed?

If I should mention 4 most important changes you should know about, it would be: 

- Version 4.0 worked fine with PHP 5.4 code - newer usually crashed it. ApiGen **can deal with PHP 5.5, 5.6, 7.0 and 7.1 code** now. 
- Min PHP version bumped **from PHP 5.4 to PHP 7.1**. ApiGen is still able to parse older code.
- **TokenReflection refactored to BetterReflection** - thanks to James and Marco for fast responses on our issues and pull-requests.
- Switched to [Symfony 3.3 Dependecy Injection](https://github.com/ApiGen/ApiGen/pull/880) with [time-saving features](https://www.tomasvotruba.cz/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/) - thanks [Martin Hujer](https://martinhujer.cz) for the idea.


Look at particular releases to get complete list of changes. Changelogs are nice and clean: 
 
- [ApiGen 5.0-RC1](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC1)
- [ApiGen 5.0-RC2](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC2)
- [ApiGen 5.0-RC3](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC3)
- [ApiGen 5.0-RC4](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC4)
- [ApiGen 5.0-RC5](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC5)


## Run ApiGen Yourself

ApiGen uses BetterReflection that is still not tagged, so you need to install it like:

```json
{
    "require-dev": {
        "apigen/apigen": "5.0.0-RC5",
        "roave/better-reflection": "dev-master#7ce58dd"
    }
}
```

and run with composer:

```bash
composer update
```

And you are read to go.


Happy generating!
