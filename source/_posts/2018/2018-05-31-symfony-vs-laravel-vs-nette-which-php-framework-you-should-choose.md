---
id: 110
title: "Symfony vs Laravel vs Nette - Which PHP Framework Should You Choose"
perex: |
    I have been asked this question over hundred times, in person, as [a post request](https://github.com/TomasVotruba/tomasvotruba.cz/issues/278). When to use Symfony? How is Laravel better than Symfony? What are Nette killer features compared to Symfony and Laravel?
    <br><br>
    Today, we look on the answer.
tweet: "New Post on my Blog: ... #cli #php"
tweet_image: "/assets/images/posts/2018/console-di/thumbnail.png"
---

## So Which PHP Framework is the Silver Bullet?

According to Wikipedia "the only weapon that is effective against a werewolf, witch, or other monsters". But also "a direct and effortless solution to a problem." Spoiler alert: **Silver bullet does not exist.** 

What does that mean in the software world? Let's say you use framework A. You have many problems - bad documentation, missing package in the core for command bus etc. - with it and you've heard that all of them solves framework B. So you migrate to framework B. Many problems solved. You use it for 2 years and  new problems appear and you've just heard that all of them solves framework C... 

<blockquote class="blockquote text-center mt-lg-5 mb-lg-5">
    There are no solutions. There are only trade-offs.
</blockquote>

### Sex for One Night vs. Love for The Lifetime

And that does not apply only framework, I've seen very good programmer moving from PHP to PHP Framework, from PHP to Python, from Python to Javascript... It's a way but has some trade-offs.

**If you have a new partner every 2 years, how well do you think you'll get to know them?** Same goes for frameworks (or any field, really).

## The Best PHP Framework based on My Experience

So which PHP framework would I recommend to you in 2018? You know from my posts and from founding PHP group "Symfonists" in the Czech Republic in 2016, that **I'm all over the Symfony**. Even though there are [many](/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/#get-rid-of-tagging-in-symfony) [things](https://github.com/symfony/symfony/pull/26686) I [disagree](/blog/2018/04/23/how-to-slowly-turn-your-symfony-project-to-legacy-with-action-injection/) with.

Luckily, I'm aware of this *confirmation bias*, so when somebody asks me the question in the title, **I think the right answer is**: 

<blockquote class="blockquote text-center mt-lg-5 mb-lg-5">
    Use what you love. There is no better way to do things.
</blockquote>

Yes, **it can be that simple**. I could give you technical arguments like:
 
- drop 3 lines of framework A code and show that in framework B it takes 20 to do the same,
- show that framework A can process 35 % more requests than framework B,
- show [a metric that is 4 times bigger](https://medium.com/@taylorotwell/measuring-code-complexity-64356da605f9) in framework B than in framework A,
- argue that this framework has 10 times more plugins, that one has autowiring and that one has not. 

But that's not really the point - it's not a contest, it's fun. Programming needs to be fun, because:
 
<blockquote class="blockquote text-center mt-lg-5 mb-lg-5">
    If you're not doing a job you want to do for the rest of your life... why are you doing it?
</blockquote>

### But Where to Start?

Ok, let's say you're looking for your first framework to fall in love with. What is the best place to start?

<br>

## The one Thing I love about Nette

<img src="https://files.nette.org/git/www/nette-logo-blue.png" width="300">
 
I really fell in love with Nette mainly because of monthly meetups called [Posobota](https://www.posobota.cz/). In English: last Saturday shortened to "LaSaturday". Maintained for last ~5 years by [Honza Černý](https://honzacerny.com/) who's keeping an awesome job of running them.

It's a meetup with 50 developers, a **regular** meetup. Regular meetups is the best place **to build deep meaningful relationships**, where you can **learn a massive amount of knowledge** over the years and **to share ideas and get feedback on them**. 

This could never happen on conferences, no matter how big. 
  
<br>

## The one Thing I love about Laravel

<img src="http://laravelblog.cz/wp-content/uploads/2017/01/co-je-laravel.png" width="300">

There is **a place where can you learn almost everything about Laravel**. It's called **[Laracasts](https://laracasts.com/)**, it's entertaining, it's short (mostly 3-5 mins long videos) and to the point. A very rare combination in software educational sources.

I got into it with free series [Be Awesome in PHPStorm](https://laracasts.com/series/how-to-be-awesome-in-phpstorm) series, where [Jeffrey Way](https://github.com/JeffreyWay) gave me the best basic about PHPStorm I could dream of. To this day I still use 70 % knowledge I learned there.

<br>

## The one Thing I love about Symfony

<img src="https://www.rostemespolecne.cz/copohwegoiwhe/uploads/2017/01/symfony_black_02.png" width=300>

What I love the most about Symfony? It has people, education, sharing, the newest information, dopamine-driven reward system... guess! 

<br>

**It's a [StackoverFlow support for Symfony](https://stackoverflow.com/questions/tagged/symfony).** I learned a lot of basic know-how there and almost all of the edge-cases. The answers are experienced and right to the point. I just love it. And when I find an outdated answer, I can improve it. That way it's clear, what was the best-proven practice in Symfony 2, in Symfony 3 and is now in Symfony 4 in one page - one example for all [How to get Parameter in Controller](https://stackoverflow.com/questions/13901256/how-do-i-read-from-parameters-yml-in-a-controller-in-symfony2). Its like [Rector](https://github.com/rectorphp/rector) just to humans.  

## It's all About You - Community

<img src="/assets/images/posts/2018/frameworks/community.jpg">

It might be weird to see that software is all about people. It's about people, emotions, relationships, what people create from their love - a post, a package, a book, a video series, a local meetup, a beer meetup, a shirt with a logo, a hat with a log, stickers, open-source package, conference, a talk...

**There would be no frameworks without you - people talking about it on meetups, online forums, people who write about it with passion in comments on Reddit.**

So just go out there on a meetup, grab a beer. No pressure, you can stay there just for 30 minutes and then go home - but try to **feel the mood and see how people work in that group**. See how that fits you. That way you'll know you have found the best framework for you, not by some random metrics. 

As they say: 

<blockquote class="blockquote text-center mt-lg-5 mb-lg-5">
    One loves vanilla ice-cream, the other pistachio.
</blockquote>
 
<br><br>

Spread love! That's all there is :)
