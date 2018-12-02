---
id: 52
title: "Shopsys, Spryker & Sylius under Static Analysis"
perex: |
     When you're building a new web project based on open-source, you'll pick a package you know, have good experience with or try a new one that might be even better.

     Lines of code, cyclomatic complexity, method count per class, length of method, number of interfaces relative to classes - these all can be just a superficial number or a <strong>quick measure how well is the project built</strong>.
tweet: "#Shopsys, #Spryker & #Sylius under Static Analysis #symfony #php #ecommerce"
tweet_image: "/assets/images/posts/2017/shopsys-static-anal/shopsys.png"
related_items: [57]

deprecated_since: "December 2018"
deprecated_message: "All 3 projects changed completely during last year, so <strong>these numbers are way outdated</strong>."
---


<br>

<blockquote class="blockquote text-center">
 "Never trust any statistics that you didn't forge yourself."
 <footer class="blockquote-footer">Nazi propaganda about Winston Churchill</footer>
</blockquote>

<br>


## Understanding Statistics


I never trust posts with statistics without any data to re-run the results on or with such a complicated methodology that discourages me to try anything and rather trust the source.

Therefore, I wanted to make this post different. To have you in control, not the hype.

### Try It Yourself

I've put all the used data and methodology in an open-source way in a [Github repository](https://github.com/TomasVotruba/shopsys-spryker-and-sylius-analysis), where **you can install it and re-run on your own**. You'll need to [ask for a access to open-beta of Shopsys Framework](https://www.shopsys.com/#contact) - that's the one extra step. I will update both this post and repository after Shopsys Framework is open-source in late 2017.


### No Black and White

The aim of posts like these is usually just to put together arguments supporting author's opinion, so they are pretty useless for you if you disagree with the author.

To be clear, I work with Shopsys as an open-source consultant. I try to be as strict and honest with them as possible in order to make the product better. And this article is another example of if.

There is no winner and no loser, as you'll see. **Let's dive into the numbers now**.
From over 50 various metrics, I've picked 4 of them. After reading them, you'll have a bit better picture of the code quality of these projects.

<br>

We are going to analyze 3 e-commerce projects build on Symfony components:

<br>

<div class="col-6 mb-3">
    <a href="https://www.shopsys.com/">
        <img src="/assets/images/posts/2017/shopsys-static-anal/shopsys.png">
    </a>
</div>

<div class="col-5">
    <a href="http://sylius.org/">
        <img src="/assets/images/posts/2017/shopsys-static-anal/sylius.png">
    </a>
</div>

<div class="col-5">
    <a href="https://spryker.com/">
        <img src="/assets/images/posts/2017/shopsys-static-anal/spryker.png">
    </a>
</div>


## 1. Lines of code

Smaller projects are easier to understand, especially when you want to send a PR and need to understand their architecture.


### Lines of code

- Shopsys: **94 590**
- Sylius: **109 111**
- Spryker: **368 245**

[Tests excluded](https://github.com/TomasVotruba/shopsys-spryker-and-sylius-analysis/blob/89ff354b5298ba831c9124039f217dd0c5687e3d/src/Finder/PhpFilesFinder.php#L27-L31).


## 2. Duplicated Code

Duplicated code can be a sign of coupled code and flaws in reusability.
**Sylius has 0,24 % duplicated lines**, Shopsys 0,67 % and lastly **Spryker with 1,20 %**.


## 3. Cyclomatic Complexity

Cyclomatic Complexity is something like a train path with switches.

**The number of paths you can take in the method.**

**A.** This is what we desire for in our code:

<div>
    <img src="/assets/images/posts/2017/shopsys-static-anal/split-2.png" class="img-thumbnail">
</div>


**B.** And this what we often end-up with:

<div>
    <img src="/assets/images/posts/2017/shopsys-static-anal/split-m.png" class="img-thumbnail">
</div>


Which one would you pick if you'd be a programmer, in a method you never saw?



Cyclomatic complexity in PHP code would, **for the example B**, look like this:

```php
final class ProductController extends Controller
{
	public function renderDetail(Request $request)
	{
		$id = $request->get('id'); # 1
		if ($id === null) { # 2
			throw new ProductIdMissingException('Id is required for product detail'.);
		}

		$product = $this->productRepository->get($id);
		if ($product === null) { # 3
			throw new ProductNotFoundException(sprintf('Product with id %d was not found.', $id);
		}

		if (! $product->isAvailable) { # 4
			$this->redirect('sold-out');
		}

		if ($this->isOnMobile()) { # 5
			foreach ($product->getImages() as $image) { # 6
				$image->resizeToMobile();
			}
		}

		$this->render('detail', [
			'product' => $product;
		]);
	}
}
```

...**6 in total**.

And for **the example A** like this:

```php
final class ProductController extends Controller
{
	public function renderDetail(Product $product)
	{
		$this->ensureIsAvailable($product); # 1
		$this->prepareForMobile($product);

		$this->render('detail', [
			'product' => $product;
		]);
	}
}
```


...with **just 1**.

This metric can give you a decent overview of hot spots that might need refactoring.
**When looking at this score, the lower the number the better**. This applies to areas as readability, maintainability and testability.

**Consider writing a unit test and having a function with a cyclomatic complexity of 12**. This means that, **if you want 100% code coverage**, you need to test every of these 12 paths and end up in pretty messy tests.

The results are:


*Shopsys*

- Complexity / Class: **2.89**
- Complexity / Method: **1.53**

*Sylius*

- Complexity / Class: **2.52**
- Complexity / Method: **1.52**

*Spryker*

- Complexity / Class: **2.46**
- Complexity / Method: **1.46**

<br>

### Extreme Is Never Good

However, having the **cyclomatic complexity = 1 in combination with low co-location** could lead to issues like [*ehnto* describes](https://news.ycombinator.com/item?id=13364649):

*"What you want is never nearby, and thanks to the Facade system used for service location it also isn't always clear which class is actually being used without digging through some configuration files...*

*This leaves you digging through dozens of tiny files, some barely longer than the class definition itself, just to find even high level logic. I normally learn frameworks by simply reading the code, but I can't help but feel fatigued when having to try and find where a line of logic actually lives in Laravel. It is almost always a journey."*

You can [read similar comparison of Code Complexity in Symfony and Laravel](https://medium.com/@taylorotwell/measuring-code-complexity-64356da605f9) by Taylor Otwell. [Maintainer vs. contributor view on Cyclomatic Complexity](https://blog.sonarsource.com/discussing-cyclomatic-complexity/) is briefly described on SonarCube.


## 4. It's All About Methods

Last metrics focuses on the most used parts of the code - methods. Even if a project is small, long and complex methods can make it very difficult to use. On the other hand: **huge projects with lines of clean and narrow methods can be fun and easy to work with**.

Let's look at the metrics of methods:


*Shopsys*

- Max. Method Length: **261 lines**
- Max. Method Cyclomatic Complexity: **12**

*Sylius*

- Max. Method Length: **26 lines**
- Max. Method Cyclomatic Complexity: **22**

*Spryker*

- Max. Method Length: **117 lines**
- Max. Method Cyclomatic Complexity: **64**


<br>


Finally, I have a question for you. Based just on these numbers and without personal preferences, **which one of the three projects would you pick and why**?


## Stay Tuned!

In the follow up post, we'll look at these projects under PHPStan and Easy Coding Standard with PSR2 rulesets.

Happy coding!
