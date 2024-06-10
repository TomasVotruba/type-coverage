# Require Minimal Type Coverage

<br>

<div align="center">
    <img src="/docs/required_type_level.jpg" style="width: 25em" alt="AI abilities sea level rising... as way to rise type coverage for class elements">
</div>

<br>

PHPStan uses type declarations to determine the type of variables, properties and other expression. Sometimes it's hard to see what PHPStan errors are the important ones among thousands of others.

Instead of fixing all PHPStan errors at once, we can start with minimal require type coverage.

<br>

What is the type coverage you ask? We have 4 type possible declarations in total here:

```php
final class ConferenceFactory
{
    private $talkFactory;
    public const SPEAKER_TAG = 'speaker';

    public function createConference(array $data)
    {
        $talks = $this->talkFactory->create($data);

        return new Conference($talks);
    }
}
```

The param type is defined, but property and return types are missing.

* 1 out of 4 = 25 % coverage

Our code quality is only at one-quarter of its potential. Let's get to 100 %!

```diff
 final class ConferenceFactory
 {
-    private $talkFactory;
+    private TalkFactory $talkFactory;

-    public const SPEAKER_TAG = 'speaker';
+    public const string SPEAKER_TAG = 'speaker';


-    public function createConference(array $data)
+    public function createConference(array $data): Conference
     {
         $talks = $this->talkFactory->create($data);

         return new Conference($talks);
     }
 }
```

This technique is very simple to start even on legacy project. Also, you're now aware exactly how high coverage your project has.

<br>

## Install

```bash
composer require tomasvotruba/type-coverage --dev
```

The package is available on PHP 7.2+ version in tagged releases.

<br>

## Usage

With [PHPStan extension installer](https://github.com/phpstan/extension-installer), everything is ready to run.

Enable each item on their own:

```yaml
# phpstan.neon
parameters:
    type_coverage:
        return: 50
        param: 35.5
        property: 70
        constant: 85
```

<br>

## Measure Strict Declares coverage

Once you've reached 100 % type coverage, make use [your code is strict and uses types](https://tomasvotruba.com/blog/how-adding-type-declarations-makes-your-code-dangerous):

```php
<?php

declare(strict_types=1);
```

Again, raise level percent by percent in your own pace:

```yaml
parameters:
    type_coverage:
        declare: 40
```

<br>

Happy coding!
