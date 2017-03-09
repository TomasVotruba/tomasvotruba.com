---
layout: post
title: "Statie 4: How to Create The Simplest Blog"
perex: '''
Statie is very powerful tool for creating small sites. But you will use just small part of it's features, having just micro-sites. How to get to full 100%? <strong>Build a blog</strong>.
    <br><br>
    Today I will show you, <strong>how to put your first post</strong>.
'''
lang: en
---


## Create a Blog Page

This might be the simplest page to show all you posts:


```html
<!-- /source/blog.latte -->

---
layout: default
---

❴block content❵
    <h1>Shouts Too Loud from My Hearth</h1>

    ❴foreach $posts as $post❵
        <a href="/❴$post['relativeUrl']❵/">
            <h2>❴$post['title']❵</h2>
        </a>
    ❴/foreach❵
❴/block❵
```

### You already see

- that all posts are in stored in `$posts` variable
- that every post has `relativeUrl`
- that every post should have a `title` (optional, but recommended)


## How Does it Work?

Statie will do 3 steps:

1. **Scans `/source/_posts` for any files**
    - those files have to be in `YYYY-MM-DD-url-title.*` format
    - that's how Statie can determine the date
2. **Converts Markdown and Latte syntax in them to HTML**
3. Stores them to `$posts` variable.


## How does a Post Content Look Like?

```twig
<!-- source/_posts/2017-03-05-my-last-post.md -->

---
title: "This my Last Post, Ever!"
---

# Begin by Letting Go

- I was always afraid of writing my feelings. I though everybody would hate me.

Not really a problem anymoogre.

I realized, *feelings are like color of skin*.
**You are born with them and they are part you nature...**
```

Save file, [look on the blog page](http://localhost:8000/blog) and see:

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/statie-4/statie-blog.png" class="thumbnail">
</div>

When you click a post title:

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/statie-4/statie-post.png" class="thumbnail">
</div>



### ProTip #1: Change url?

You see the url for the post is:

```
blog/2017/03/05/my-last-post/
```

or

```
blog/Year/Month/Day/FileSlug
```

This **can be changed by configuration**. Create `config.neon` and override default values:

```yaml
<!-- source/_config/config.neon -->

configuration:
    postRoute: blog/:year/:month/:day/:title
```

Where `:year`, `:month`, `:day` and `:title` are all variables.

For example:

```bash
configuration:
    postRoute: my-blog/:year/:title
```

Would produce url:

```
my-blog/2017/my-last-post/
```

Go it? I know you do! **You are smart.**


### ProTip #2: Show last 3 on homepage?

- todo: find on tomasvotruba.cz history


In next post, I will you some cool post features.


## Now You Know

- **That all posts are placed in `/source/_posts` directory and in `$posts` variable**.
- That post has to be in **named as `YYYY-MM-DD-title.md` format**
- That you can change the post generated url in `source/config/_config.neon` in "postRoute".


Happy coding!
