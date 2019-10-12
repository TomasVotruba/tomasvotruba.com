---
id: 222
title: "Still on PHPUnit 4? Don't You Worry, You Make It Double"
perex: |
    Last week I was on [PHPSW meetup](https://twitter.com/akrabat/status/1181998973588037632) and shared Rector tool with UK PHP folks for the first time. To my surprise, Nette to Symfony migration under 80 hours we did in January was not the biggest value the folks at meetup saw.
    <br><br>
    **Upgrading PHPUnit was**. So I was thinking, let's take it from the floor in one go, from PHPUnit 4 to the latest PHPUnit 8.

tweet: "New Post on #php 🐘 blog: Still on PHPUnit 4? Come to PHPUnit 8 Together"
---


Min PHP version

<table class="table table-bordered table-responsive">
    <thead class="thead-inverse">
        <tr>
            <th>PHPUnit</th>
            <th>min PHP version</th>
        </tr>
    </thead>
    <tr>
        <td>PHPUnit 4</td>
        <td>PHP 5.5</td>
    </tr>
    <tr>
        <td>PHPUnit 5</td>
        <td>PHP 5.6</td>
    </tr>
    <tr>
        <td>PHPUnit 6</td>
        <td>PHP 7.0</td>
    </tr>
    <tr>
        <td>PHPUnit 7</td>
        <td>PHP 7.1</td>
    </tr>
    <tr>
        <td>PHPUnit 8</td>
        <td>PHP 7.2</td>
    </tr>
</table>

Is PHPUnit 9 out?
See [current minimal PHP version](https://phpunit.de/supported-versions.html).



## PHPUnit 4 to 5
* david mention
* phpunit bridge
* look at rector issues

## PHPUnit 5 to 6

## PHPUnit 6 to 7

## PHPUnit 7 to 8

Add new cache file to `.gitignore`

```
.phpunit.result.cache
```

In the end, you should see at least PHPUnit 8.5+

```bash
vendor/bin/phpunit --version
$ PHPUnit 8.5.15 by Sebastian Bergmann and contributors.
```


That's it!

run rector first than upgrade to the version in composer
