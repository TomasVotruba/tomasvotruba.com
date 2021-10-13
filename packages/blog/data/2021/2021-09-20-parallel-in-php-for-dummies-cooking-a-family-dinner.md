---
id: 336
title: "Parallel in PHP for Dummies? Cooking a Family Dinner"
perex: |
    Would you like to understand parallel in PHP apps?
    <br>
    Do you have just **60 seconds**?
tweet: "New Post on the 🐘 blog: Parallel in PHP for Dummies? Cooking a Family Dinner"
tweet_image: "/assets/images/posts/2021/dinner_cooking.jpg"
---

Imagine you're cooking dinner for your family, and you **miss 4 ingredients** to make the meal tasty.

Onion, garlic, pepper and chilli:

<img src="/assets/images/posts/2021/dinner_cooking.jpg" class="img-thumbnail mt-2 mb-4" style="max-width: 20em">

Thank God you have exactly 2 kids that want to help you to cook your favorite meal!

<img src="/assets/images/posts/2021/kids.jpg" class="img-thumbnail mt-2 mb-4" style="max-width: 20em">

You could send them both to get 4 ingredients, but it would be faster if each of them could get just 2 ingredients.

You're rushing them to get it, and after they leave, you realize they forgot their phones. **They can't talk to you, and they can't talk to each other**. We don't know when they'll be back, if they have enough money or if they found what you need.

**We have to wait till everyone gets back to see the result**.

<br>

## How does this Story Look in PHP code?

```php
// 1. what we have and what we want?
$neededIngredients = ['onion', 'garlic', 'pepper', 'chilli'];
$familyMembers = ['son', 'daughter'];

// 2. tell the instructions
$familyMembersCount = count($familyMembers);
$ingredientsChunks = array_chunk($neededIngredients, $familyMembersCount)

// 3. let them free and wait for the result
$foundIngredients = [...];
foreach ($familyMembers as $key => $familyMember) {
    $ingredientsChunk = $ingredientsChunks[$key];
    $foundIngredients[] = $familyMember->findIngredients($ingredientsChunk);
}

return $foundIngredients;
```

<br>

And that's precisely how parallel works ECS!

## From Food to Technical Terminology

* 1 family member = 1 CPU thread
* 1 needed ingredient = 1 input file
* ingredients chunk (2 items) = array of input files for 1 CPU thread

That's it! Now you understand parallel processes.

<br>

Happy coding!
