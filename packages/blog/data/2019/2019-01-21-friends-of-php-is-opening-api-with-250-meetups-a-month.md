id: 179
title: "FriendsofPHP.org is Opening API with&nbsp;250&nbsp;Meetups&nbsp;a&nbsp;Month"
perex: |
    [Friendsofphp.org](https://friendsofphp.org) already checks **over 1 145 PHP groups** on meetup.com for new meetups every day. That's about 240 meetups in a single month - a great number, but some user groups don't use meetup.com.
    <br><br>
    I spend last weekend adding 4 new sources for meetups... and while doing it, I thought: "why not make all that meetups and groups public in JSON"?

tweet: "New Post on #php 🐘 blog: FriendsofPHP.org is Opening #API with 250 Meetups a Month"
tweet_image: '/assets/images/posts/2019/fop/fop.png'
---

If you want to organize meetups on Meetup.com, it will cost you 10 $/month. Not every organizer can afford that, mainly in starting or small communities.

That's why there are other free platforms that collect meetups - each with their own way to export these meetups:

 - [crossweb.pl](https://crossweb.pl/feed/wydarzenia/php) with xml feed in Poland
 - [dou.ua](https://dou.ua/calendar/feed/PHP) with xml feed in Ukraine
 - [posobota.cz](webcal://www.posobota.cz/feed.ical.php) with ical in the Czech Republic
 - [opentechcalendar.co.uk](https://opentechcalendar.co.uk/api1/events.json) **with json** in UK

XML feeds are "fun" to work with. In dou.ua, the feed is so bad, that it contains only meetup name and its detail URL - no place or time. So you have to **crawl each detail URL standalone** and parse hidden JSON in HTML. But that's not all. Sometimes the json is but sometimes it isn't, so you have to actually parse the file and use XPath:

```php
<?php

$jsonData = $crawler->filterXPath('//script[@type="application/ld+json"]/text()');
if ($jsonData->getNode(0) === null) { // has some result?
    return null;
}

try {
    return Json::decode($jsonData->text(), Json::FORCE_ARRAY);
} catch (JsonException $jsonException) {
    // parse the whole site and get crawl HTML content
    return null;
}
```

opentechcalendar.co.uk from UK is much better at this, providing json:

```json
"data":[{"slug":7847,"slugforurl":"7847-machine-learning-adoption-enhancing-and-automating","summary":"Machine Learning Adoption: Enhancing and Automating Decision Making","summaryDisplay":"Machine Learning Adoption: Enhancing and Automating Decision Making","description":"","deleted":false,"cancelled":false,"is_physical":true,"is_virtual":false,"custom_fields":{"code_of_conduct":null},"siteurl":"https:\/\/opentechcalendar.co.uk\/event\/7847-machine-learning-adoption-enhancing-and-automating","url":"https:\/\/www.eventbrite.co.uk\/e\/machine-learning-adoption-enhancing-and-automating-decision-making...
```

But, how well can we read that? With a bit of lagging... we can understand it.

## The Simplest API Possible

**I'm lazy to read anything I don't need for my work**. I won't look and instantly know the keys I can use. Are there coordinates or do I have to geolocate them from the city?

```json
{
    "meetups": [
        {
            "name": "Poslední Sobota 101",
            "userGroup": "Posobota",
            "start": "2019-01-26 15:00",
            "city": "Praha",
            "country": "Czech Republic",
            "latitude": 50.0874654,
            "longitude": 14.4212535,
            "url": "http://www.posobota.cz"
        }
    ]
}
```

I want simple URL, with all the data I need. Just load and parse in 2 lines.

- No guzzle
- No auth nor tokens
- No back and forth HTML crawling
- No XML entities to string conversion

Simple like this:

```php
<?php

use Nette\Utils\FileSystem;
use Nette\Utils\Json;

$jsonContent = FileSystem::read('https://friendsofphp.org/api/meetups.json');
$json = Json::decode($jsonContent, Json::FORCE_ARRAY);

var_dump($json['meetups']); // all meetups on friendsofphp.org
```

## Complains Everywhere... What about Me?

All right, all this API is clearly bad and the authors probably never used them themselves. But what about friendsofphp.org API? Is it good?

Well, there is none. Better bad API than no API → crawling and Xpaths. Better done than perfect. So to make this right...

**...I published all meetups and groups for anyone to use in API**.

<img src="/assets/images/posts/2019/fop/fop.png" class="img-thumbnail mb-5">

With API you can list meetups in your country, sort them by a distance from your city, get all groups from your neighbor countries, render a map with Wordpress meetups... or whatever comes to your mind. It's all up to your creativity.

## API with Statie?

With *encoded knowledge* approach, you don't have to think about pretty JSON, or studying XML vs JSON format. Just provide data and let program handle it.

This website runs on [Statie](https://www.statie.org) - the most downloaded PHP static website generators supporting Twig and Markdown. Why don't we Statie handle this as well?

Since Statie 5.4-dev, you can do this:

```yaml
# statie.yaml
parameters:
    api_parameters:
        - 'groups'
        - 'meetups'
```

That way, their parameters will be turned into API feeds:

```bash
/api/groups.json
/api/meetups.json
```

With this beautiful json:

```json
{
    "meetups": [
        {
            "name": "Poslední Sobota 101",
            "userGroup": "Posobota",
            "start": "2019-01-26 15:00",
            "city": "Praha",
            "country": "Czech Republic",
            "latitude": 50.0874654,
            "longitude": 14.4212535,
            "url": "http://www.posobota.cz"
        }
    ]
}
```

That way you can publish a list of talks, books you've read or package you've made. **You don't have to think** about the design, URL, nor creating own templates and handling the empty output.

**Statie got you covered!**

<img src="/assets/images/posts/2019/fop/gotit.jpg" class="img-thumbnail">

<br>

How do you use API feeds? What is your approach to provide them?
