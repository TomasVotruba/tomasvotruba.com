---
id: 7
title: Autowired&nbsp;Controllers as Services for&nbsp;Lazy&nbsp;People
perex: "With new autowiring feature in Symfony 2.8+, it is now easier to manage dependencies for services. But what about for controllers? Unfortunately, there are 3 annoying steps you have to do. Today I will show you, how to reduce them to 0."

updated_since: "May 2017"
updated_message: |
    This post is valid only up to Symfony 3.2.

    Since **Symfony 3.3** you can use [PSR4-based service discovery and registration](https://github.com/symfony/symfony/pull/21289).
    It does pretty much the same thing - **registers autowired controllers** (and more) - and it has native support in Symfony.

    I recommend using it instead!
---

### Disclaimer: Why even use controllers as services?

**The goal of this article is not to discuss pro and cons of "controller as service" (further CAS) approach**. If you
haven't decided yet to use CAS, I recommend checking these articles:

- [Symfony2: Make my Controllers Services?](https://knpuniversity.com/screencast/question-answer-day/controllers-services) [released 2013 by KNPUniversity]
- [Symfony2: How to create framework independent controllers?](http://php-and-symfony.matthiasnoback.nl/2014/06/how-to-create-framework-independent-controllers) [released 2014]
- [Symfony2: Controller as Service](http://richardmiller.co.uk/2011/04/15/symfony2-controller-as-service) [released 2011]

<br>

But now, back to the topic.

## Autowire service? Easy!

With [autowire feature](https://dunglas.fr/2015/10/new-in-symfony-2-83-0-services-autowiring), managing dependencies for services is now as simple as:

```yaml
services:
    post.publisher:
        class: PostPublisher
        autowire: true # all you got to do is add this line
```


## Autowire controller? Hell!

Managing dependencies for controllers in same way is complicated. To apply the same effect, you have to make following 3 steps:

1. Register controller manually as service to the config

    ```yaml
    # app/config/services.yml
    services:
        post_controller: # you have to use this name everywhere, so pick it wisely
            class: PostController
            autowire: true
    ```

2. Add `@Route` annotation with service name

    ```php
    // src/AppBundle/Controller/PostController.php
    namespace AppBundle\Controller\PostController;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

    /**
     * @Route(service="post_controller") # watch for typo here!
     */
    class PostController
    {
        public function listAction()
        {
        }
    }
    ```

    or route using service name:

    ```yaml
    # app/config/routing.yml
    post_list:
        path: /post-list
        defaults:
            _controller: post_controller:listAction
            # and not bundle like approach
            # _controller: AppBundle:Post:list
    ```

    > This difference is so difficult to spot, that it [created question on StackOverflow](https://stackoverflow.com/a/27221394/1348344).

3. Finally, you have to use service name and single colon for referring:

    ```php
    // any controller
    $this->forward('post_controller:listAction'));
    ```

> There is [nice answer on StackOverflow explaining with more details](https://stackoverflow.com/questions/31366074/how-exactly-can-i-define-a-controller-as-service-using-annotations/31366589#31366589).

This process is exhausting already and difficult to remember.

### Did you make it? Here comes much deeper hell

Even if you do manage to finish these steps, **these issues will appear**:

- [drawback](https://stackoverflow.com/questions/33857659/symfony-autowiring-services-with-the-controller) of FrameworkBundle, when it tries to autowire controller
    <img src="http://i.stack.imgur.com/r4cBD.png">

- it's complicated to apply constructor dependency injection for extended 3rd party controllers (Sonata, FOS...), due to missing step 2 and 3 (that were mentioned above) and the "bundle naming" inside the bundle's code

### Is there some way back from hell to heaven?

Author of autowiring feature and Symfony core contributor Kévin Dunglas [sees similar problem](https://github.com/symfony/symfony/pull/16863#issuecomment-162221353) and [proposes solution with ADR pattern](https://dunglas.fr/2016/01/dunglasactionbundle-symfony-controllers-redesigned). I think it's the right direction, but it bends controllers too much.

But the goal is to keep controllers' code without touching.

## Autowiring in Controllers

This is wheremade [Symplify\ControllerAutowire](https://github.com/Symplify/ControllerAutowire) bundle helps. It solves problems mentioned above.

How does it work?

- find controllers in `/src` directory
- register them as services
- autowire its constructors
- handle routing properly for both "service name" and "bundle name" approaches
- and all on compile time

Let's try it together.

### 1. Install package

```yaml
composer require symplify/controller-autowire
```

### 2. Register bundle

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ControllerAutowire\SymplifyControllerAutowireBundle(),
            // ...
        ];
    }
}
```

### 3. Add some dependency for your controller via constructor

```php
// src/AppBundle/Controller/DefaultController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $this->eventDispatcher->dispatch('someEvent');

        return $this->render('default/index.html.twig', [
            // ...
        ]);
    }
}
```

And that's it!

For further use, **just check Readme for [Symplify/ControllerAutowire](https://github.com/Symplify/ControllerAutowire).**

<br>

Happy coding!
