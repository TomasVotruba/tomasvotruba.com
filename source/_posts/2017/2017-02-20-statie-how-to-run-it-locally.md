---
id: 29
title: "Statie: How to run it Locally"
perex: |
    <a href="https://github.com/Symplify/Statie">Statie</a> is a tool to create semi-static webpages. It allows you to host your website on Github for free. Event with own domain and https. It was created in late 2016 based on Scuplin and its major feature is simplicity.
    <br><br>
    That was the "pitch", now I will show you how to use it.
related_items: [32, 33, 34]
tweet: "#Statie 1: Up and Running locally #php #static #github"
---

### Create Empty Project...

...and require [Statie](https://github.com/Symplify/Statie) package.


```bash
composer require symplify/statie
```



### 3 Steps Life-Cycle

Statie has very simple life-cycle:

1. Create code in `/source` directory, using HTML, Latte and Markdown
2. Generate HTML code via console
3. See the generated HTML output code in `/output` via local PHP server

Later, you can push output code to Github Pages or your server via FTP or SSH. But let's start with small step.


## 1. Create your Code

Statie supports **HTML**, **[Latte](https://github.com/nette/latte)** (like Smarty or Twig, just bit different syntax) and **Markdown** (in `.md` files only).

For most of pages, HTML and Latte is enough. Markdown is useful for repeated items like posts.

Let's create our index page.

```html
<!-- source/index.latte -->

Hi!
```

### Use Layout if you Like

```html
<!-- source/_layouts/default.twig -->

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        {block content}{/block}
    </body>
</html>
```

And add layout and block content to the `index.latte`:

```html
---
layout: default
---

{block content}
    Hi, my job is to help you grow any direction you choose!
{/block}
```

**Section between `---` is used to configure system or own variables**. I will tell you more about that later.


## 2. Generate Final HTML Code

Run in project root in CLI:

```bash
vendor/bin/statie generate source/
```

## 3. See Generate Code

Create PHP local server in CLI:

```bash
php -S localhost:8000 -t output
```

and open [localhost:8000](https://localhost:8000) in your browser.

Voilá!


<div class="text-center">
    <img src="/assets/images/posts/2017/statie-1/statie-cycle.gif" style="width:100%">
</div>


## Minitip: Use Gulp Work For You

It is quite annoying to refresh regenerate content manually every time you change the code.

Simple Gulp script can regenerate content for use.

Install `gulp` and `gulp-watch`:

```bash
npm install -g gulp gulp-watch
```

```javascript
var gulp = require('gulp');
var watch = require('gulp-watch');
var exec = require('child_process').exec;

gulp.task('default', function () {
    // Run local server, open localhost:8000 in your browser
    exec('php -S localhost:8000 -t output');

    // Generate current version
    // For Windows use: exec('vendor\\bin\\statie generate', function (err, stdout, stderr) {
    exec('vendor/bin/statie generate', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
    });

    return watch(['source/**/*', '!**/*___jb_tmp___'], { ignoreInitial: false })
    // For the second arg see: https://github.com/floatdrop/gulp-watch/issues/242#issuecomment-230209702
        .on('change', function() {
            // For Windows use: exec('vendor\\bin\\statie generate', function (err, stdout, stderr) {
            exec('vendor/bin/statie generate', function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
            });
        });
});
```

And run the script via:

```bash
gulp
```

Now open [localhost:8000](http://localhost:8000) and change `source/index.latte`.

It works!
