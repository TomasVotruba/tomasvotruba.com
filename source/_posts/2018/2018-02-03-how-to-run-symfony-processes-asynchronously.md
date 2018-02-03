---
id: 75
title: "How to Run Symfony Processes Asynchronously"
perex: '''
    ...
'''
tweet: "New post on my blog: How to Run #Symfony Processes Asynchronously"
---

@todo test?

post - How to run multiple symfony process asynchronously


It takes quite a long time to split monorepo packages: exactly **420 s for 8 packages** of Symplify.

*Although there are faster way like [splish/lite](https://github.com/splitsh/lite), we aim on PHP + Git combination to allow PHP developers to extend the code and not.*


- We needed to publish 

### Process Run One by One

```php
foreach ($splitConfiguration as $directory => $repository) {
	$process = new Process(sprintf('git subsplit %s:%s', $directory, $repository));
	$process->run();

	// here the process is finished
	if (! $process->isSuccessful()) {
		throw new PackageToRepositorySplitException($process->getErrorOutput());
	}

	// report exactly what happened, so it's easier to know result and debug 
	$this->symfonyStyle->success(sprintf(
		'Split from "%s" to "%s" is done',
		$directory,
		$repository
	));
}
```


We tried [spatie/async](@todo) but it turned out to be not solid, but rather magic [by passing service as serialized string to CLI that desirializes it and runs on own thread](@todo) causing other process commands fail on success message. It is probably good enought for Laravel, but not for me.


### What are the other options?

We could go [amp](...) or [reactphp](...), but wouldn't that be an overkill?

Symfony Process is already stadalone process in own thread and allows [asynchronous runs](http://symfony.com/doc/current/components/process.html#running-processes-asynchronously).


### What we actually needed?

1. To run all procceses at once
2. Wait untill they're finished
3. Report their success/error status


### 1. To run all proccesses at once

```php
$runningProccesses = [];

foreach ($splitConfiguration as $directory => $repository) {
	$process = new Process(sprintf('git subsplit %s:%s', $directory, $repository));
	$process->start();

	$runningProccesses[] = $process;
}
```

### 2. Wait untill they're finished

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

###  3. Report their success/error status

```php
$this->symfonyStyle->success('Split was successful');
```

But how useful is this message compared to previous one? And what if any processes failed?

```php
$this->symfonyStyle->success(sprintf('Split from "%s" to "%s" is done', $directory, $repository));
```

Let's improve this: 

```diff
$runningProccesses = [];
+$allProcessInfos = [];

foreach ($splitConfiguration as $directory => $repository) {
	$process = new Process(sprintf('git subsplit %s:%s', $directory, $repository));
	$process->start();

	$runningProccesses[] = $process;
+	// value object with types would be better/faster here, but for sake of example, array is used
+	$allProcessInfos[] = [
+		'process' => $process,
+        'directory' => $subdirectory,
+        'repository' => $repository
+	];
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

    $this->symfonyStyle->success(sprintf(
        'Push of "%s" directory to "%s" repository was successful',
        $processInfo['directory'],
        $processInfo['repository']
    ));
}
```


### 203 % Faster Performance

<a href="https://github.com/Symplify/Symplify/pull/620" class="btn btn-dark btn-sm">
    <em class="fa fa-github fa-fw"></em>
    See pull-request #620
</a>

@todo screens from PR


420 s
=>
139 s


That's it!

<br><br>

pyHpa async nurs!

