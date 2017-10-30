---
id: 36
layout: post
title: "3 Symfony and Laravel Patterns that Make Code Easy to Extend Without Modification"
perex: '''
    Do you write open-source? If so, you probably get many PR and issues about adding new feature, that people miss.
You can add them and increase project complexity or deny them and increase people's frustration. Both sucks for somebody. I prefer win-win situations: <strong>keep complexity low and add new features</strong>.
    <br><br>
Magic? No, <strong>just patterns</strong>! Today we look on 3 of them I found and fond in Symfony and Laravel world.
'''
tweet: "3 #symfony and #laravel patterns to make your code #SOLID #php"
---

## How is Open-Source Different from Private Code

There is a big mind-shift from closed-source to open-source. To make it really work, you need to move from *my ego first* to *other people's feelings first*.

It is like building Matrix open to everybody. **You have to predict future and unexpected use cases**. Your code have to be **extendable without making any changes in it**.

### "Opened for Extension, Closed for Modification"

Now, I can refer to [**Open/closed principle** on Wikipedia](https://en.wikipedia.org/wiki/Open/closed_principle), which is the worst way to explain it.

Instead, **I took a time to find simple example** (pro tip: Google with "simple") and actually found one - [go check it](https://github.com/wataridori/solid-php-example/blob/b84657cb736f86dda1453061d15df01f260e5140/2-open-closed-principle.php#L20-L32), it clearly shows wrong approach.

Today I will show you 3 ways to create such entrances.


## 1. Interfaces for Everything

When I first saw Laravel, I've noticed one big difference to other PHP projects I've seen. There is [Contracts](https://github.com/laravel/framework/tree/master/src/Illuminate/Contracts) directory, that contains **interface for every service there is in Laravel**. And they're not only in this directory, but used everywhere in the code. Crazy move by [Taylor Otwell](https://medium.com/@taylorotwell) in that times, but very useful.

### Why is this Useful?

- You don't have to think about every service, if it could be replaced or not.
- People **will love your package**, because they will **see freedom and understanding**, not obedience as in other packages. That's what happened for me.

### Don't forget to have Final Word

To promote using interfaces instead of extending your classes like this:

```php
class BoothCallEntrance implements MatrixEntranceInterface
{

}

class ComputerEntrance extends BoothCallEntrance
{

}
```

**always [mark your classes final](https://ocramius.github.io/blog/when-to-declare-classes-final/)**. There is event [sniff for that](https://github.com/Symplify/CodingStandard/blob/master/src/Sniffs/Classes/FinalInterfaceSniff.php). Use it.

```php
final class BoothCallEntrance implements MatrixEntranceInterface
{

}

final ComputerEntrance implements MatrixEntranceInterface
{

}
```

Programmers won't have to think about raping your classes in the night - **they just use the interface you provide**.

<div class="text-center">
    <img src="/assets/images/posts/2017/extendable-open-source/overide.jpg" class="img-thumbnail">
</div>

**This is code-embodied composition over inheritance**. No documentation nor Wikipedia links required.



## 2. Go to Party Events, when in the Mood

Back to Matrix world: imagine you can listen to every phone booth. Let's say you **write a script, that sends you sms with geo location of the booth everytime it gets called** (favorite tool for agent Smith ;-)).

<div class="text-center">
    <img src="/assets/images/posts/2017/extendable-open-source/booth.png" class="img-thumbnail">
</div>

This approach is implemented in PHP under name of EventDispatcher. While working with Symfony, **events gave me very similar feeling of freedom** - [in docs](http://symfony.com/doc/current/reference/events.html#kernel-events) as well in small book [A Year with Symfony](https://leanpub.com/a-year-with-symfony).

Do you want simple example of such listening script? [Check this tested post](https://pehapkari.cz/blog/2016/12/05/symfony-event-dispatcher/) with all code snippets you need.

### While on Event, Listen Carefully

Matrix situation above would look like this:

Code of your package:

```php
// some CRON script checking all booths are working

if ($booth->isCalled()) {
    // you with people could get here without sending you a PR for everything they might need? Easy! ↓

    // this is the entry point, just listen to 'boothCall'
    $this->eventDispatcher->dispatch('boolCall', $booth->getLocation();
    // ...
}
```

And custom script listening:

```php
final class BoothSpy
{
    public function listenTo()
    {
        return 'boothCall';
    }

    public function runOnPing($location)
    {
        $this->smsSender->sendABoothAlert($location);
    }
}
```

### Why is this Useful?

- You can introduce entry point via event.
- You can **also pass metadata**, like location. Those data **can be open to change**, but don't have to be.

It might be confusing while using at first, but after few weeks I get used to it. Trust me, it's the best.


## 3. Like Collecting stamps, just on Steroids


This is most powerful and less known architecture pattern.

**1 service collects all services of specific type**


### Where it came from?

Do you know [service tagging in Symfony](http://symfony.com/doc/current/reference/dic_tags.html)?


```yaml
services:
    app.custom_subscriber:
        class: AppBundle\EventListener\CustomSubscriber
        tags:
            - { name: kernel.event_subscriber }
```

All services of `EventSubscriber` type are collected by EventDispatcher.


### You Probably Already Use It

- Console Commands → Console Application
- [Security Voters](http://symfony.com/doc/current/security/voters.html) → Access Decision Manager


As for tags - [they often promote bad practise of duplicated information](https://www.tomasvotruba.cz/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/#bare-tagging-is-duplicated-information). Don't use it if you don't have to.


### Why is this Useful?

- It goes very well with step 1.
- **It's easy to integrate into huge systems**. All you need to do is add 1 line of code:

```yaml
services:
    - YourSubscriber
```
- It gives you powers of constructor injection. **Your service can use any other services.**
- It's the best prevention and antidote to [God classes](http://sahandsaba.com/nine-anti-patterns-every-programmer-should-be-aware-of-with-examples.html#god-class).


### Back to the Matrix

<div class="text-center">
    <img src="/assets/images/posts/2017/extendable-open-source/renderer.jpg" class="img-thumbnail">
</div>

Let's say we have a service to render Matrix. It might look like this:

```php
final class MatrixRenderer()
{
    public function render()
    {
        $this->agents->render();
        $this->environment->render();
        $this->people->render();
    }
}
```

Later, one customer wants to render night clubs. Another customer wants to add weather. How to do that without modifying the code?  Like this:


```php
final class MatrixRenderer()
{
    /**
     * @var LayerRendererInterface[]
     */
    private $layerRenderers = [];

    public function addLayerRenderer(LayerRendererInterface $layerRenderer)
    {
        $this->layerRenderers[] = $layerRenderer;
    }

    public function render()
    {
        foreach ($this->layerRenderers as $layerRenderer) {
            $layerRenderer->render();
        }
    }
}
```

And decouple to services:

```yaml
services:
    - AgentLayerRenderer
    - EnvironmentLayerRenderer
    - PeopleLayerRenderer

    # added by customers
    - NightClubsLayerRenderer
    - WeatherLayerRenderer
```



*Do you want to use collectors without pain? In case you use Symfony, Nette or Laravel, here is [PackageBuilder](https://github.com/Symplify/PackageBuilder/blob/54ca56f850867b5ba9c5d96d2a00f4e2f0bb63a4/src/Adapter/Symfony/DependencyInjection/DefinitionCollector.php) that makes it simple.*


### How do You Make your Packages Easy to Extend?

Let me know if you use any of these patterns. Or do you use something else? I'd love to hear about that!


