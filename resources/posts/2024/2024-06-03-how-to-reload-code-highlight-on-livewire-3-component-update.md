---
id: 412
title: "How to reload code highlight on Livewire&nbsp;3 component&nbsp;update"
perex: |
    I use Livewire 3 for interactive forms and fast clickable maps. Last week, I worked on a filter page for the Rector website, where you can use text input to search for core and community rules.

    I typed "add param" to the input and got the results that best matched the rules I could use. But the code highlight was gone; what now?

    Current proposed solutions in Laracast/Livewire forums are miss-leading or using old Livewire 2 syntax, so I wanted to share the solution here to make it easier to find.
---

*Thanks to [MrPunyapal](https://github.com/rectorphp/getrector-com/pull/2300) and [SanderMuller](https://github.com/rectorphp/getrector-com/pull/2301) for tightening the final solution presented here.*

I use Javascript code highlighter and am very happy with its simple use and maintenance in the community. *It just works* for many years straight, and I appreciate this type of feature more and more.

Whether it's highlight.js, CodeMirror, or any other similar package, they all provide simple configuration: It says, "highlight everything with class X":

```javascript
document.querySelectorAll('pre code.language-diff').forEach((element) => {
    hljs.highlightElement(element);
});
```

To make highlighter work at the right time, there must be an HTML to decorate in the first place. Let's wrap it in a `DOMContentLoaded` event listener:

```javascript
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('pre code.language-diff').forEach((element) => {
        hljs.highlightElement(element);
    });
});
```

We're all familiar with this setup. Now, Livewire 3 comes in.

<br>

## Livewire 3 component

We have a simple form text input that invokes component refresh on typing:

```html
<input type="text" wire:model.live="query" placeholder="Search for a rule">
```

If you're familiar with Livewire 2, you'll notice the new `.live` directive. It invokes the component refresh on type, not just submit (disabled by default in Livewire 3).

In the component, we filter rules as we type:

```php
use Livewire\Component;

final class RectorFilterComponent extends Component
{
    public ?string $query = null;

    public function render()
    {
        // filter rules here
        $filteredRules = ...;

        return view('livewire.rector-filter-component', [
            'filteredRules' => $filteredRules,
        ]);
    }
}
```

<br>

It works well, but the code **highlighter is turned off on refresh**:

<video controls width="600" class="img-thumbnail">
    <source src="https://github-production-user-asset-6210df.s3.amazonaws.com/924196/336463733-74aaff55-ea27-473f-900f-b42b53c0dfac.webm?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAVCODYLSA53PQK4ZA%2F20240605%2Fus-east-1%2Fs3%2Faws4_request&X-Amz-Date=20240605T064416Z&X-Amz-Expires=300&X-Amz-Signature=8563f2836fbd96576e541b5d3d518c84923d3933802f2e0373b1c597e1ad78fe&X-Amz-SignedHeaders=host&actor_id=924196&key_id=0&repo_id=108684833" type="video/webm">
            Your browser does not support the video tag.
</video>

<br>

We want it to keep the code highlighted as we type, not only after page refresh.

<br>

## How to reload Highlighter on Component update?

We'll use a Livewire feature that makes communication between PHP and JS accessible - events.

First, we dispatch an event in the `render()` method:

```diff
 use Livewire\Component;

 final class RectorFilterComponent extends Component
 {
     public ?string $query = null;

     public function render()
     {
         $filteredRules = ...;

+        $this->dispatch('rules-filtered');

         return view('livewire.rector-filter-component', [
             'filteredRules' => $filteredRules,
         ]);
     }
 }
```

Now, we submit a "rules-filtered" event to both the PHP and JS worlds.

<br>

Then we update the component template with Javascript that listens to our event:

```diff
 document.addEventListener('DOMContentLoaded', function () {
+    document.addEventListener('rules-filtered', () => {
+        requestAnimationFrame(() => {
             document.querySelectorAll('pre code.language-diff').forEach((element) => {
                 hljs.highlightElement(element);
             });
+        });
+    });
 })
```

## What is happening here?

* First, we listen to the `DOMContentLoaded`, so we have the full HTML available
* Then we listen to our custom "rules-filtered" event - this code will invoke only when after we dispatch it in the component
* When we have HTML and the event is dispatched, let's run our highlighter to decorate the code with colors

That's it!

<br>

Mind the [`requestAnimationFrame()`](https://github.com/rectorphp/getrector-com/pull/2300/files#r1626252225). It solves the blinking between bare black and white code and the highlighted code. The HTML will be only rendered once it's fully highlighted. Fancy!

<br>

Happy coding!
