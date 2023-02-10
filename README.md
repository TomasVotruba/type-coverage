# Require Minimal Type Coverage

<br>

<div align="center">
    <img src="/docs/required_type_level.jpg" style="width: 25em" alt="AI abilities sea level rising... as way to rise type coverage for class elements">
</div>

<br>

PHPStan uses type declarations to determine the type of variables, properties and other expression. Sometimes it's hard to see what PHPStan errors are the important ones among thousands of others.

Instead of fixing all PHPStan errors at once, we can start with minimal require type coverage.

<br>

What is the type coverage you ask? We have 3 type possible declarations in total here:

```php
final class ConferenceFactory
{
    private $talkFactory;

    public function createConference(array $data)
    {
        $talks = $this->talkFactory->create($data);

        return new Conference($talks);
    }
}
```

The param type is defined, but property and return types are missing.

* 1 out of 3 = 33 % coverage

How do we get to the 100 %?

```diff
 final class ConferenceFactory
 {
-    private $talkFactory;
+    private TalkFactory $talkFactory;

-    public function createConference(array $data)
+    public function createConference(array $data): Conference
     {
         $talks = $this->talkFactory->create($data);

         return new Conference($talks);
     }
 }
```

This technique is very simple and useful to start with even on legacy project. You also know, how high coverage your project has right now.

<br>

## Install

```bash
composer require tomasvotruba/type-coverage --dev
```

The package is available on PHP 7.2-8.1 versions in tagged releases.

<br>

## Usage

With [PHPStan extension installer](https://github.com/phpstan/extension-installer), everything is ready to run.

Enable each item on their own with simple configuration:

```neon
# phpstan.neon
parameters:
    type_coverage:
        return_type: 50
        param_type: 30
        property_type: 70
        print_suggestions: false
```
