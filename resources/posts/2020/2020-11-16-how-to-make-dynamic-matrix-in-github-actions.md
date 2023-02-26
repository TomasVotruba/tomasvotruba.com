---
id: 288
title: "How to make a Dynamic Matrix in GitHub Actions"
perex: |
    Do you want [split monorepo](/blog/2020/11/09/new-in-symplify-9-monorepo-split-with-github-action) for each package?
    Instead of 20 workflows with copy-paste steps, you can define **just one with a static matrix for packages**.


    Yet, nothing in real life is static but rather dynamic. A new package can be added, old can be removed.
    How could we automate this even more with a **dynamic matrix?**

---

In the last post we talked about [monorepo split](/blog/2020/11/09/new-in-symplify-9-monorepo-split-with-github-action) with GitHub Actions.

Today we'll look on a rather general idea for any GitHub Action - **dynamic matrix**.

## Static Matrix

We've already talked about the use case for the split of many packages into many repositories. Instead of repeating each workflow with a different package, we can use **a static matrix**.

A typical static matrix looks like this:

```yaml
jobs:
    monorepo_split:
        runs-on: ubuntu-latest

        strategy:
            matrix:
                package:
                    # list your packages here
                    - coding-standard
                    - phpstan-rules
```

After you define the `strategy`, add steps that use `${{ matrix.package }}`.

```yaml
        # ...
        steps:
            -   uses: actions/checkout@v2

            -
                uses: symplify/github-action-monorepo-split@master
                env:
                    GITHUB_TOKEN: ${{ secrets.ACCESS_TOKEN }}
                with:
                    package-directory: 'packages/${{ matrix.package }}'
                    split-repository-organization: 'symplify'
                    split-repository-name: '${{ matrix.package }}'
```

For each item in `package:` a new workflow run will be triggered. So, in this case - matrix **triggers 3 parallel runs**.

<br>

## Matrix is an Array

The above could be written in PHP like:

```php
$matrix = [
    'packages' => [
        'coding-standard',
        'phpstan-rules',
    ]
];

foreach ($matrix['packages'] as $package) {
    $packageDirectory = 'packages/' . $package;
    $splitRepositoryName = $package;
    // ... do the magic
}
```

Is there new package? Add it to the matrix:

```diff
            matrix:
                package:
                    # list your packages here
                    - coding-standard
                    - phpstan-rules
+                   - easy-coding-standard
```

That's it!

<br>

Any static opens up the door to troubles... How easy do you think **is to [forget to extend](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/)** the workflow **after** adding a new package?

## From Static Array to JSON

GitHub Actions are ready to make our life easier. Since April 2020, there is [a `fromJson()` function](https://github.blog/changelog/2020-04-15-github-actions-new-workflow-features/#new-fromjson-method-in-expressions) to help us. What does it do?


It converts a json to an array, like this:

```diff
            matrix:
-                 package:
-                   - coding-standard
-                   - phpstan-rules
+                 package: ${{ fromJson(["coding-standard", "phpstan-rules"]) }}
```

You are probably thinking, *"Well, that's just terrible, Tomas"*. Thank you, and you're right.

- But where is JSON, there is... an endpoint.
- **And where is an endpoint, there is a space for dynamic output**.

The [Symplify\MonorepoBuilder](https://github.com/symplify/monorepo-builder) is using this trick to **get all the packages from `/packages` directory in handy json format**:

```bash
vendor/bin/monorepo-builder packages-json
```

↓

```json
[
    "coding-standard",
    "phpstan-rules"
]
```

This version is not final, but very roughly the command above would be written like this:

```diff
-           matrix: ${{ fromJson(["coding-standard", "phpstan-rules"]) }}
+           matrix: ${{ fromJson(vendor/bin/monorepo-builder packages-json) }}
```

## From Json to Fully Dynamic Matrix

If we run the command above as it is, it would fail. Setting up a matrix is like the `setUp()` method in a PHPUnit test case - there is zero code executed before it.

- **That's why we use `setUp()` method** to actually *set* what we need first.
- Then run `test()` method.

<br>

We have to do the same here:

- In the 1st step, we create the JSON with all packages
- In the 2nd step, we use this JSON as input for the matrix, **that will create a standalone run for each package**

### How does the Workflow look Like?

```yaml
jobs:
    # phase 1
    provide_packages_json:
        runs-on: ubuntu-latest

        steps:
            # git clone + use PHP + composer install
            -   uses: actions/checkout@v2
            -   uses: shivammathur/setup-php@v2
                with:
                    php-version: 7.4
            -   run: composer install --no-progress --ansi

            # here we create the json, we need the "id:" so we can use it in "outputs" bellow
            -
                id: set-matrix
                run: echo "::set-output name=matrix::$(vendor/bin/monorepo-builder packages-json --names)"

        # here, we save the result of this 1st phase to the "outputs"
        outputs:
            matrix: ${{ steps.set-matrix.outputs.matrix }}

    # phase 2
    split_monorepo:
        # this means, wait for the "provide_packages_json" phase 1 to finish
        needs: provide_packages_json

        runs-on: ubuntu-latest
        strategy:
            # ↓ the real magic happens here - create dynamic matrix from the json
            matrix:
                package: ${{ fromJson(needs.provide_packages_json.outputs.matrix) }}

        steps:
            # ...
```

That's it!

This way, we'll **never forget** to split nor test a new package. Never.

<br>

Check fully functional [`.github/workflows/split_monorepo.yaml`](https://github.com/symplify/symplify/blob/master/.github/workflows/split_monorepo.yaml) in Symplify monorepo for more details.

Is this something niche? Not really. This week, Kamil [has added this approach to Sylius too](https://github.com/Sylius/Sylius/blob/3464e8d0ae6673d9ee1da3d538a6b399cfcb9852/.github/workflows/packages.yml#L48).

## Use for ~~Monorepo Split~~ Anything Dynamic!

This `fromJson()` trick is not something exclusive to monorepo package splitting. It's just one of the possible use cases.

The primary use case **is already in your mind**.

- What is repeated static in your CI?
- What lists can be delegated to simple command that already knows the data?
- **How could you use the dynamic matrix in your workflows today?**

<br>

Happy coding!
