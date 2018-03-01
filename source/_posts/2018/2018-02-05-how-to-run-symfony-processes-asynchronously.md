---
id: 75
title: "How to Run Symfony Processes Asynchronously"
perex: '''
    It takes quite a long time to split Symplify [monorepo](https://github.com/Symplify/Monorepo) packages: exactly **420 s for 8 packages** of Symplify.
    <br><br>
    Could we go 200 % faster by putting processes from serial to parallel?
'''
tweet: "New post on my blog: How to Run #Symfony Processes Asynchronously #async"
tested: true
test_slug: ProcessAsync
---

## Process Run One by One

This is our code now. Each process waits on each other - one is finished, then next starts.

```php
foreach ($splitConfiguration as $directory => $repository) {
	$process = new Process(sprintf('git subsplit %s:%s', $directory, $repository));
	$process->run();

	// here the process is finished
	if (! $process->isSuccessful()) {
		throw new PackageToRepositorySplitException($process->getErrorOutput());
	}

	// report exactly what happened, so it's easier to know result and debug
	$symfonyStyle->success(sprintf(
		'Split from "%s" to "%s" is done',
		$directory,
		$repository
	));
}
```

## How to Go Async in PHP?

We tried [spatie/async](https://github.com/spatie/async) which has very nice README at first sight and works probably very well for simple functions. But it turned out to be rather magic [by passing service as serialized string](https://github.com/spatie/async/blob/master/src/Runtime/ParentRuntime.php) to [CLI that desirializes it and runs on own thread](https://github.com/spatie/async/blob/master/src/Runtime/ChildRuntime.php). It also caused other process commands fail on success message. It is probably good enought for Laravel, but not for my [SOLID standards](https://github.com/jupeter/clean-code-php#solid).


### What are the Other options?

We could go [amp](https://github.com/amphp/amp) or [reactphp](https://reactphp.org/), but wouldn't that be an overkill?

There is also faster way like [splitsh/lite](https://github.com/splitsh/lite), but **we aim on PHP + Git combination so PHP developers could extend the code**.

Luckily, Symfony Process already **allows [standalone process](https://symfony.com/doc/current/components/process.html#running-processes-asynchronously)** without waiting on each other.

## What We Actually Need?

Picking the right tool is important, since it vendor locks our code to package, but lets step back a little.

**What is the exact goal we need?**

1. Run all processes at once
2. Wait untill they're finished
3. Report their success/error status

## 1. To run all Processes at Once

```php
$runningProccesses = [];

foreach ($splitConfiguration as $directory => $repository) {
	$process = new Process(sprintf('git subsplit %s:%s', $directory, $repository));
	// start() doesn't wait until the process is finished, oppose to run()
	$process->start();

    // store process for later, so we evaluate it's finished
	$runningProccesses[] = $process;
}
```

This foreach starts all processes in parallel. Without knowing they're finished or not.

**Don't forget to check that your CPU is not burned by running many processes at once** by limiting concurrency.
In our case it's only 8, so we survive this.

## 2. Wait Until They're Finished

```php
while (count($activeProcesses)) {
	foreach ($activeProcesses as $i => $runningProcess) {
		// specific process is finished, so we remove it
		if (! $runningProcess->isRunning()) {
			unset($activeProcesses[$i]);
		}

        // check every second
        sleep(1);
    }
}

// here we know that all are finished
```

##  3. Report their Success/Error Status

```php
$symfonyStyle->success('Split was successful');
```

But how useful is this message compared to previous one?

```php
$symfonyStyle->success(sprintf(
    'Split from "%s" to "%s" is done',
    $directory,
    $repository
));
```

And what if any processes failed?

### Let's improve this

```diff
 $runningProccesses = [];
+$allProcessInfos = [];

 foreach ($splitConfiguration as $directory => $repository) {
     $process = new Process(sprintf('git subsplit %s:%s', $directory, $repository));
     $process->start();

	 $runningProccesses[] = $process;
+    // value object with types would be better and faster here, this is just example
+    $allProcessInfos[] = [
+        'process' => $process,
+        'directory' => $subdirectory,
+        'repository' => $repository
+    ];
 }
```

So final reporting would look like this:

```php
foreach ($allProcessInfos as $processInfo) {
	/** @var Process $process */
	$process = $processInfo['process'];
    if (! $process->isSuccessful()) {
        throw new PackageToRepositorySplitException($process->getErrorOutput());
    }

    $symfonyStyle->success(sprintf(
        'Push of "%s" directory to "%s" repository was successful',
        $processInfo['directory'],
        $processInfo['repository']
    ));
}
```


### Speed up From 420 to 139 s

Symplify has 8 packages to build at the moment. Putting split commands to async had amazing improvement!

<a href="https://github.com/Symplify/Symplify/pull/620" class="btn btn-dark btn-sm">
    <em class="fa fa-github fa-fw"></em>
    See pull-request #620
</a>



That's it!

<br><br>

pyHpa ansyc rusn!

