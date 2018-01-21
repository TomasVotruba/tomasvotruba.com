---
id: 73
title: "..."
perex: '''
...
'''
tweet: "New post on my blog: ..."
tweet_image: "..."
---



resources:

- https://symfony.com/blog/new-in-symfony-3-4-local-service-binding
- https://www.tomasvotruba.cz/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/

paramters to controller



# 1

before

$this->container->getParameter('...')

since 2.8 auotowirign services were used more and more in dependency injectino way (@see post David Grudl)




# it work,s but it breaks encapslution

imagine oyu need a servcie in conrolelr. you'll use contruco inejction


```php

<?php

class LectureController extends Controller
{
	public function __contruct(....)


	public function signInAction()
	{
		//...

	}
}
``` 


then you need a paramter



```php

<?php

class LectureController extends Controller
{
	public function __contruct(....)


	public function signInAction()
	{
		$paymentBankAccount = $this->container->getParamter('...');

		//...

	}
}
``` 

This completely wrong, not because dependency injection is better than service locator, but because code becomes inconsistent. It's not clear:

- when should I used construoct injection?
- when should I use servie locator?


When your collegaue will extnds your code 3 motnhs later, it might [broke your window](@todo link theory):

```php

<?php

class LectureController extends Controller
{
	public function __contruct(....)


	public function signInAction()
	{
		$paymentBankAccount = $this->container->getParamter('...');

		//...

	}

	public function refundAction()
	{
		$this->container->get(RefundService);
	}
}
``` 

## Consistency over per-file pattern

You understand your code, you know reasons why it's written this way and the boundaries when to use dpeendy injeiton and service (or pamater) locator.

But that's you. Other people have no pre-cognitive understangin of your code. They read the code and learn while reading. Nothign more. That's why it's important to use as less as rules possible to prevent cognitive overload (@todo link). which lead to poor understand and coding furhter in own way, not related to original code much.

## DI is the Flow, Go with it

Also ,since symfony 3.4 and 4.0 (and their di feature (@links)) there is evolution to the useful and developer friendly dependency injeciton paragigm.

## So what it would look like?

When service is passed via contucotr, as it's dependency create in container, why not the parameter, which is alo set in config outside the controller.

```php

<?php

class LectureController extends Controller
{
	public function __contruct(...., paramter)


	public function signInAction()
	{
		$paymentBankAccount = $this->paramter;

		//...

	}
	}
}
``` 

### how would that look like in config?

We need to:

- register controller manually
- pass the paramter to contructor
- autowiret the rest

It's lot of work, but it's worth it.

```yml
# services.yml
parameters:
	bankAccount: '1093849023/2013'

servicies:
	_defaults:
		autowire: true

	App\Controller\LectureController:
		arguments:
			- '%bankAccount%'
```


Would you use this approach? 5 lines for 1 parameter in 1 service?
What about 2, 3 or 40 controllers using it?

```yml
servicies:
	autowire: true

	App\Controller\LectureController:
		arguments:
			- '%bankAccount%'

	App\Controller\ContactController:
		arguments:
			- '%bankAccount%'

	# and 40 more services with manual setup
	App\Model\PaymentService:
		arguments:
			# with care when used with another position then 1st one
			2: '%bankAccount%'
```

Doh, so much work :(

I'd be closing the answer right now settling back to much simpler:

```php
$this->container->getParameter('bankAccount');
```

No wait, there is simpler way!


Since Symfony 3.3 we can usePSR4 (@todo link) autoladog and 3.4 parametr binding (@todo link).

So previous steps got much simpler:

- <strike>register controller manually</strike> → use PSR4 **once for all services**
- <strike>pass the paramter to contructor</strike> → use binding **once for all services**
- autowire the rest

```yml
servicies:
	autowire: true
	App\Controller\:
		resource: ..

	bind:
		$bankAccount: %bankAccount%
```


Now you can add 50 more services using $bankAccount as ctor dependency with no extra edit on config. Win-win!



## This post is tested, is will last forever (almost)


The final code is tested (see it githbu diretoryx @too link) and is the best solution in time being - Symfony 3.4 and 4.0.
Without test, it would get obsolete by Symfony 5, but people would be still using it - like happens with long-tailed answer in stackfover - https://stackoverflow.com/questions/13901256/how-do-i-read-from-parameters-yml-in-a-controller-in-symfony2

But thanks to tests that are always run under the newest, it will get updates with just a little work.

To make this post as useful as possible for a long as possible



Read here more about tested posts and their essential need in programming blogging (@todo link pehapkari)
