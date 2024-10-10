---
id: 412
title: "How to reload code highlighter on Livewire 3 component update"
perex: |
    I use Livewire 3 for interactive forms and fast clickable maps. Last week, I worked on a filter page for the Rector website, where you can use text input to search for core and community rules.

    I typed "add param" to the input and got the results that best matched the rules I could use. But the code highlight was gone; what now?

    Current proposed solutions in Laracast/Livewire forums are miss-leading or using old Livewire 2 syntax, so I wanted to share the solution here to make it easier to find.
---

*Thanks to [MrPunyapal](https://github.com/rectorphp/getrector-com/pull/2300) and [SanderMuller](https://github.com/rectorphp/getrector-com/pull/2301) for tightening the final solution presented here.*

I use Javascript code highlighter and am very happy with its simple use and maintenance in the community. *It just works* for many years straight, and I appreciate this type of feature more and more.

Whether it's [highlight.js](https://highlightjs.org/), [CodeMirror](https://codemirror.net/), or other similar package, they all provide simple configuration - "highlight everything with class X":

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

It works well, but the code **highlighter is turned off on refresh**.

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

Mind the [`requestAnimationFrame()`](https://github.com/rectorphp/getrector-com/pull/2300/files#r1626252225). It **solves the blinking** between bare black/white code and the highlighted code. The HTML will be only rendered once it's fully highlighted. Fancy!

<br>

Happy coding!
