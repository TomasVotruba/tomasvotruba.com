---
id: 32
title: "Symfony Static Dumper - part 2: How to add Contact Page With Data"
perex: |
    [In previous post](/blog/2017/02/20/statie-how-to-run-it-locally/) we generated simple index with layout. Today we look on first dynamic feature - **parameters**.

updated_since: "February 2021"
updated_message: |
    Updated with **Symfony Static Dumper**, YAML → PHP, Symfony configs syntax and PHP 8.

tweet: "New Tweet on Lazy Blog: #symfony Static Dumper - part 2: How to add Contact Page With Data"
---

## Contact Page with Socials Accounts Data Separated

First, create a `contact.twig` file in the `/templates` directory:

```html
{% extends "_layouts/default.twig" %}

{% block content %}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        <li>Email: <a href="mailto:hi@gmail.com">hi@gmail.com</a></li>
        <li>Twitter: <a href="https://twitter.com/wise-programmer">@wiseProgrammer</a></li>
        <li>Github: <a href="https://github.com/wise-programmer">@WiseProgrammer</a></li>
    </ul>
{% endblock %}
```

Second, create a controller that render this file:

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ContactController extends AbstractController
{
    #[Route(path: 'contact', name: 'contact')]
    public function __invoke(): Response
    {
        return $this->render('contact.twig');
    }
}
```

<br>

## How to Refactor to Parameters?

We are programmers and we don't like data coupled to the code. You wouldn't put your repository class to your `Homepage.twig` template, would you?

What if...

- we want to **add new contact**,
- **change it**
- or **use in multiple parts** of website.

First, we modify the template to work with dynamic `contact_methods` variable:

```html
{% block content %}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        {% for contact_method in contact_methods %}
            <li>
                {{ contact_method.type }}:
                <a href="{{ contact_method.link }}">{{ contact_method.name }}</a>
            </li>
        {% endfor %}
    </ul>
{% endblock %}
```

Second, we add a new parameter to `config/config.php`:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $contactMethods = [
        [
            'type' => 'Email',
            'link' => 'mailto:hi@gmail.com',
            'name' => 'hi@gmail.com'
        ], [
            'type' => 'Twitter',
            'link' => 'https://twitter.com/wise-programmer',
            'name' => '@wiseProgrammer'
        ], [
            'type' => 'Github',
            'link' => 'https://github.com/wise-programmer',
            'name' => '@WiseProgrammer'
        ]
    ];

    $parameters = $containerConfigurator->parameters();
    $parameters->set('contact_methods', $contactMethods);
};
```

Third, we use this parameter in controller to pass it to the template:

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ContactController extends AbstractController
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
    }

    #[Route(path: 'contact', name: 'contact')]
    public function __invoke(): Response
    {
        return $this->render('contact.twig', [
            'contact_methods' => $this->parameterBag->get('contact_methods')
        ]);
    }
}
```

Save the file, [look on the contact page](http://localhost:8000/contact) and works!

<div class="text-center">
    <img src="/assets/images/posts/2017/statie-2/statie-contact.png" class="img-thumbnail">
</div>

## Now You Know

- How to add parameter to your Symfony Static Dumper page.
- **Where to put them**
- That its basically Symfony config convention

<br>

Happy coding!
