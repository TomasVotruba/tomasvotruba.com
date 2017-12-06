---
id: 4
title: "The Best of SymfonyCon 2015"
perex: '''
    Annual SymfonyCon held this year in Paris. Despite all the attacks, Paris lives on. Maybe more. An example of this can be 1000 visitors who arrived at the event. I represented Bohemia with Dennis, and also with Petr and Cuba from Lmc.eu.
'''
---

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-divadlo.jpg" class="img-thumbnail">
    <br>
    <em>The queue on the Symfony Elephpants. No more left in 2 hours.</em>
</div>
<br>

And now to the point. There were over 20 lectures in the beautiful theater and affiliated cinema.

Today we will look at the 4 best.

## 1. 10 years of Symfony

The Symfony have been with us for 10 years. Although the project was founded by Fabien Potencier, it is worth more and more personalities that move it towards greater clarity and usability.

Fabien mentioned over 20 people he had met on his journey and showed who contributed to the Symphony. It sounded like a toy. Very inspiring for those who want to scale their open-source projects and indulge in their long life.

### 1 Thing Worth Remembering

Among others, he introduced a [new release process](http://symfony.com/blog/improving-the-symfony-release-process), that will facilitate <em>continuous upgrades</em>.

<div class="text-center">
    <img class="img-thumbnail" src="/assets/images/posts/2015/symfonycon/symfonycon-release-process.jpg">
</div>
<br>

Simply put:

- with every new major version the old one comes out
- both will be LTS
- both will have the same feat
- as well as 2.8 and 3.0

The transition to the new major version will be under much greater control than ever before.
     

## 2. Symfony2 at BlaBlaCar

<a href="https://speakerdeck.com/odolbeau/symfony-at-blablacar" class="btn btn-warning btn-sm">
    <em class="fa fa-slideshare fa-fw"></em>
    See Slides
</a>

This seemingly random-generated company deals with blah blah blah... ridesharing, that is, together.

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-bla-bla-car.png" class="img-thumbnail">
</div>
<br>

This was a case study firm that started out as a small startup, growing fast and learning to scroll along the march. They tried lots of ways that did not work to find the right ones (for their context, of course).

It's about these "do's and dont's" that our lecturers shared:

- upgrade as soon as you can
- failure is beginning
- Doctrine - fast, but issues with MVC, cache, integrity problems, scale, decoupling...
- Event Dispatching in RabbitMQ

### 1 Thing Worth Remembering

The more varied the development of other applications where it usually passes monolithic repository for separate microservices using the REST API?

Microservices not use their internal APIs that they came too difficult to maintain, but their own solutions.

They call him **The Gateway** ([slajd](https://speakerdeck.com/odolbeau/symfony-at-blablacar?slide=64)) and its advantages are DDD, separation of business logic and data přístwupu and transparent organization. Interesting idea.


## 3. New Symfony Tips and Tricks

<a href="http://www.slideshare.net/javier.eguiluz/new-symfony-tips-tricks-symfonycon-paris-2015" class="btn btn-warning btn-sm">
    <em class="fa fa-slideshare fa-fw"></em>
    See Slides
</a>


And now something for everyone:

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-tips-and-tricks.jpg" class="img-thumbnail">
</div>
<br>

[Javier Eguiluz](https://twitter.com/javiereguiluz), whom we all know as:

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-javier.png" class="img-thumbnail">
</div>
<br>



He shared with us about tips and tricks that gathered over the last year - not only for writing [Week of Symfony](http://symfony.com/blog/category/a-week-of-symfony).

For me it was the most interesting lecture from which I took a large number of tips in their own practice.

**Tips are for beginners and advanced**, such as

- nested Doctrine transactions
- custom logger formatter - no ugly confusing logos

The lecture was full of useful tips that simply could not absorb all at once.

I was pleased that it was even a few tips from [Martin Hasoň](https://twitter.com/hasonm).

This inspired me a great overview and blink on [the version from last year](http://www.slideshare.net/javier.eguiluz/symfony-tips-and-tricks).


### 1 Thing Worth Remembering


If you assume an application without testing and want to add at least some control, will you throw "smoke testing" for all services:

```php
public function testContainerServices()
{
	$client = static::createClient();

	foreach ($client->getContainer()->getServiceIds() as $serviceId) {
        $service = $client->getContainer()->get($serviceId);
        $this->assertNotNull($service);
	}
}
```


## 4. Symfony: Your next Microframework

<a href="http://www.slideshare.net/weaverryan/symfony-your-next-microframework-symfonycon-2015" class="btn btn-warning btn-sm">
    <em class="fa fa-slideshare fa-fw"></em>
    See Slides
</a>


Ryan Weaver is awesome. Surely you know him even you - from Symfony blog, which he gives a gripping read. And just as gripping and funny (maybe even funnier) is in person. His lecture that Symfony can now be used as microframework (thanks [MicroKernelTrait](https://github.com/symfony/symfony/blob/3.0/src/Symfony/Bundle/FrameworkBundle/Kernel/MicroKernelTrait.php)) was absolutely great.


He showed us new ways and means...

- as having "multiple applications" in one project,
- how to make a minimalist website with all the magical widgets Symfony,
- and most importantly, how to kill Silex because this lecture him totally whacked.

On this issue will soon come out at the source article, where you show everything in detail.

 

### 1 Thing Worth Remembering


Previously, the choice was clear:

- Silex small and simple applications
- Symfony for larger ones

Thus, when used MicroKernel?

- MicroKernel comes in handy when you start a small application, but want it in time scale. In addition to Silex supports Bundle.

 

### Other Talks Worth of Click

- *Marc Morena* - When e-commerce meets Symfony ([see slides](http://www.slideshare.net/MarcMorera/when-ecommercemeetssymfonyparissymfonycon2015))
- *Benjamin Eberlei* - Doctrine 2 - to use or not to use ([see slides](https://qafoo.com/resources/presentations/symfonycon_paris_2015/doctrine2_to_use_or_not_to_use.html))

**All available slides from the conference to find [Joind.in](https://joind.in/event/symfonycon-paris-2015/schedule/list)**.


## What are my Takeaways?

In addition to the 5 Elephants, 1 T-shirts and contacts to the lead developers of open-sources projects, I met at the conference...

- with the latest trends in Symfony and other large projects
- with how to build a community built on equality and people
- with people with open-source environment, which I previously knew only from the avatar on GitHub

A lot of taste experiences bizarre French cuisine :).

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-stage.jpg" class="img-thumbnail">
</div>
