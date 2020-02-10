---
id: 176
title: "9 Steps to Migrate From Jekyll to Statie"
perex: |
    Jekyll is great a way to start static website on Github Pages. But Jekyll has one big problem - the language.
    How would you add custom Twig or Latte filter to Jekyll?
    <br><br>
    I wanted to migrate my static websites from Jekyll to Statie. Can new `init` command make this piece of cake? And what needs to be done next?
tweet: "New Post on #php 🐘 blog: 9 Steps to Migrate from #Jekyll to #Statie"
---

## 1. Create Basic Statie Structure

Statie 5.3 brings [new `init` command](/blog/2019/01/07/how-to-create-your-first-php-twig-static-website-under-2-minutes-with-statie/), that creates basic structure in `/source` directory, `statie.yml`, `.travis.yml` and metafiles.

Before we start any moving, create a basic structure to save many copy-pasting steps:

```bash
composer require symplify/statie
vendor/bin/statie init
```

Then, clean `/source` directory from generated files and...

## 2. Move Source files to `/source` Directory

- Jekyll has all the source code in the root.
- Statie works with `/source` directory, so the website is separated from PHP code, tests, metafiles.

```diff
-CNAME
+/source/CNAME
```

```diff
-index.html
+/source/index.html
```

```diff
-_data/projects.yaml
+/source/_data/projects.yaml
```

## 3. Move Parameters Files Under `parameters > [param name]` Sections

**Before** - Jekyll

```yaml
# _data/projects.yaml
-
    name: Symplify
    url: https://github.com/Symplify/Symplify
```

**After** - Statie

```yaml
# source/_data/projects.yaml
parameters:
    projects:
        -
            name: Symplify
            url: https://github.com/Symplify/Symplify
```

## 4. Upgrade Absolute Links to Moved Files

```diff
-https://github.com/TomasVotruba/gophp71.org/edit/master/_data/projects.yaml
+https://github.com/TomasVotruba/gophp71.org/edit/master/source/_data/projects.yaml
```

## 5. Load Moved YAML Files in `statie.yml`

```diff
+imports:
+    - { resource: "source/_data/projects.yaml" }
```

## 6. Remove `site.data.` and use Variables Directly

```diff
 <ul>
-    {% for project in site.data.projects %}
+    {% for project in projects %}
         <li><a href="{{ project.url }}">{{ project.name }}</a></li>
     {% endfor %}
 </ul>
```

## 7. Setup Github Pages deploy in Travis

Thanks to `vendor/bin/statie init` you have correctly configured `.travis.yml` in your repository.

To finish deploy, you need to:

- create `gh-pages` branch
- pick it as a source for Github Pages
- generate Github Token
- put it to Travis configuration of your repository

How you do this? Just **follow [Statie.org documentation](https://www.statie.org/docs/github-pages/)** step by step.

## 8. Clean Metadata from Headers

In Jekyll, it's required to have `---` section in files, even if empty. You can drop it now:

```diff
- ---
- ---

HTML
...
```

## 9. Run Project Locally

This is what I missed the most at Jekyll page - instant feedback. We want to develop and see output instantly - is it correct or is there a bug?

```bash
npm install
gulp
```

- Then open `localhost:8000` to see your generated HTML.
- Did you edit code in `/source`? → Just **refresh browser to see re-generated HTML**.

This is at least 100 times faster than deploying to Jekyll and checking the output in the production.

### Show me the Real Migration Code

- [pull request on gophp71.org](https://github.com/DeprecatedPackages/gophp71.org/pull/32)
- [pull request on gomonorepo.org](https://github.com/DeprecatedPackages/gomonorepo.org/pull/7)

<br>

That's it! You can enjoy Markdown, Twig and PHP directly from your local machine and still on Github Pages.

Happy coding!
