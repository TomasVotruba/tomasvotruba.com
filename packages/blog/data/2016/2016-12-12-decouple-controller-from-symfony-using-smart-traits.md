---
id: 18
title: "Decouple Controller from Symfony using Traits"
perex: "If you start using controllers as services, you still <strong>often need helpers methods of Controller from FrameworkBundle</strong>. So your code still depends on service locator and decoupling is not really happening.<br><br>Today I will show you <strong>how to remove the dependency on Controller and keep those fancy methods at the same time</strong>."

deprecated_since: "May 2017"
deprecated_message: |
    Since **Symfony 3.3** you can use [AbstractController](https://github.com/symfony/symfony/pull/22157). It does pretty much the same thing - **in even cleaner way** - and it has native support in Symfony.

    I recommend using it instead!
---

## Typical use case of Mixing Principles

1. Constructor Injection
2. Service locator (container) from parent Controller class

Today it's not about which one is better and when to use it, but about consistency. I recommend either using first or second. Mixing them together is confusing and adds complexity without bringing value or improving readability.

Let's see an example:

```php
namespace Symfony\Bundle\FrameworkBundle\Controller;

final class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    // constructor injection
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function actionList()
    {
        // using parent method that uses service locator (container)
        return $this->render('mytemplate.html.twig', [
             'products' => $this->productRepository->fetchAll()
        ]);
    }
}
```

Seeing this code anyone could easily extends it like this, and I would not blame him.

```php
public function actionList()
{
    return $this->render('mytemplate.html.twig', [
         'products' => $this->productRepository->fetchAll(),
         // using parent method that uses service locator (container)
         'title' => $this->get('title_manager')->fetchForProducts()
    ]);
}
```


## Real Issue? See 3 PRs to Symfony Core

There are some trials to make this bit more decoupled:

- [[DependencyInjection] Autowiring: add setter injection support](https://github.com/symfony/symfony/pull/17608), but is was [reverted](https://github.com/symfony/symfony/pull/20384)
- [[FrameworkBundle] Split abstract Controller class into traits](https://github.com/symfony/symfony/pull/16863) - opened in December 2015 and without attention
- [[FrameworkBundle] Introduce autowirable ControllerTrait](https://github.com/symfony/symfony/pull/18193) - similar to previous one, but depending on reverted setter injection

There are some trials, but I guess **this feature is not coming anytime soon**.


### How to make a Change

When it's **difficult to estimate if this would be useful feature to merge**, somebody creates a bundle and test it in practise.

Similar thing happened for [knpuniversity/KnpUGuardBundle](https://github.com/knpuniversity/KnpUGuardBundle) that later became core part of Symfony.

So...

### Is There a Bundle for This?

Few months ago [Petr Olišar](https://twitter.com/PetrOlisar) reported [similar issue](https://github.com/symplify/symplify/issues/14) to [ControllerAutowire](https://github.com/Symplify/ControllerAutowire) package.

I realized it perfectly fits there and **I added it**.

Thank you Petr!

## How Does it Work?

Now you can do use bare Controller with only what you need:

```php
use Symplify\ControllerAutowire\Controller\Templating\ControllerRenderTrait;

final class ProductController
{
    use ControllerRenderTrait;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function actionList()
    {
        return $this->render('mytemplate.html.twig', [
             'products' => $this->productRepository->fetchAll()
        ]);
    }
}
```

**You can use few small traits** or **just 1** - `ControllerTrait`. Pick what fits your needs the best.

You will [find them in repository](https://github.com/Symplify/ControllerAutowire/tree/master/src/Controller) or just type `ControllerTrait` in your IDE:

## Try This Out and Let Us Know

There were many suggestions around those PR, but no package to really try it out in  practice.

So feel free to install this package and try this out. Feedback is much appreciated.

**It will be helpful in PRs to Symfony core and helps to make conscious decision about this topic**.
