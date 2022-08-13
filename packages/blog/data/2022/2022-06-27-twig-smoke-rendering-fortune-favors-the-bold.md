---
id: 364
title: "Twig Smoke Rendering - Fortune&nbsp;Favors&nbsp;the&nbsp;Bold"
perex: |
    In the previous post, we set on [journey of fails](/blog/twig-smoke-rendering-journey-of-fails) with ups and downs. Well, mostly downs. I'm trying to be honest about the blind path process behind the final published work.
    <br><br>
    We made it through heaven and hell. Now we're rested and back to continue. Can we smoke render twig templates, or shall we give up?

tweet_image: "/assets/images/posts/2022/frodo_end.jpg"
---

After 5 challenging steps we made last time, we need to recharge our fuel. This episode will be more relaxing and more fun.

<br>

## 6. Way Too Tolerant Constants

Do you use constants in your TWIG files? Then you've come across this issue:

```twig
{{ constant("YourneyPath::WOODS") }}
```

This filter [calls native `constant()` function](https://github.com/twigphp/Twig/blob/760341fa8c41c764a5a819a31deb3c5ad66befb1/src/Extension/CoreExtension.php#L1369).

<br>

Few years later, we rename the  constant in the `YourneyPath` class:

```diff
 final class YourneyPath
 {
-    const WOODS = 'woods;
+    const FOREST = 'forest;
 }
```

We trust the IDE to handle renames for us, so we will not check any other files or templates.

<br>

Since PHP 8.0, we would get a Fatal error on missing constant, but [PHP 7.4 and below is only warning](https://3v4l.org/NMPXC) 🚫

<br>

**How do we make the `constant()` filter strictly on lower PHP versions?**

In the same way, we made filters/functions input tolerant. We add a modified filter:

```php
$constantFunction = new TwigFunction('constant', function ($constant) {
    if (defined($constant) === false) {
        throw new \Exception(sprintf('Constant "%s" not found', $constant));
    }

    return constant($constant);
}));
```

<br>

Now we carefully catch any missing constant on any TWIG and PHP version.

<img src="/assets/images/posts/2022/frodo_and_sam.jpg" class="img-thumbnail" style="max-width: 30em">

## ✅

<br>

## 7. Where is the Form?

We have covered functions, filters, and variables. Now, most edge-cases **depends on the specific features you use from TWIG**.

In our case, there is one feature that will throw us a curve ball:

```twig
{% form_theme form "some_theme.twig" %}
```

What is this element?

<br>

It's pretty hard to Google, but in the end, we found out it's a "token parser". It's a **way to parse TWIG syntax between `{% ... %}`** to native PHP code. This *token* is called "form_theme" and sets a style theme only for the current form.

<br>

When we run this part of the code, we get an error:

```bash
The "form" variable is missing
```

<br>

## Faking Token Parser?

We tried to **replace** [**token parser according** to Twig Documentation](https://twig.symfony.com/doc/2.x/advanced.html#defining-a-token-parser), but failed.

In the end, we realized we use a `$form` variable as a name for any form. There was no other name used, and templates were perfectly isolated from each other. We come up with the simple solution of adding a simple form:

```php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SimpleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder->add('name', TextType::class);
    }
}
```

<br>

Then we provide it the render method by default:

```php
$simpleFormType = $this->formFactory->create(SimpleFormType::class);
$context['form'] = $simpleFormType->createView();

$environment->render($templateName, $context);
```

It feels a bit hacky, but it works, and we can control our form easily without pre-parsing the templates with regexes.

<br>

## ✅

<br>

## Fortune Favors the Bold

After 2 weeks of hard work, but mainly counter-intuitive thinking and countless mad experiments, we have **a working tool that can smoke parse any of 200 template files we have** in 2 seconds:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">We&#39;ve just added our latest toy to CI😋 with ✅<br><br>Tolerant <a href="https://twitter.com/hashtag/twig?src=hash&amp;ref_src=twsrc%5Etfw">#twig</a> renderer 🎉🎉🎉<br><br>* covers all TWIG files<br>* dynamic rendering<br>* finds non-existing filters + functions + tags<br>* even wrong constants!<br>* blazing fast ⚡️<br>* no php-parser, no magic transform<br>* fun to make 😎 <a href="https://t.co/5H8iXVNyGS">pic.twitter.com/5H8iXVNyGS</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1540004210888040452?ref_src=twsrc%5Etfw">June 23, 2022</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

Based on this 3-post series, you can build your own tailored TWIG, Latte, or Blade Smoke Renderer. It might not be easy at the start, but if you make it, **you'll enjoy sweet fruits**.


<br>

## Believe in Yourself

Next time there is a challenge that presents itself, it will look as impossible, crazy, and weird. Think about it differently, be naive, even crazy. Take on your journey; believe you can make it, and you will.

<img src="/assets/images/posts/2022/frodo_end.jpg" class="img-thumbnail" style="max-width: 30em">

These experiences are priceless stories to share.

<br>

Happy coding!
