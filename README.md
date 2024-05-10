# Laravel DeepL Translator

Provides access to DeepL API for Laravel projects

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

## Table of contents

* [Installation](#installation)
* [Using](#using)

## Installation

To get the latest version of `Laravel DeepL`, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require dashed/laravel-deepl
```

Or manually update `require` block of `composer.json` and run `composer update`.

```json
{
    "require": {
        "dashed/laravel-deepl": "^0.1"
    }
}
```

## Using

Translate single string

```php
use Dashed\Deepl\Facades\Deepl;

$translated = Deepl::api()->translate('Hallo wereld!', 'NL', 'EN');
```


Create/Update lang files for specific language. It allows to complete all missing translations for specific language.

```shell
php artisan deepl:sync nl
```

[badge_downloads]:      https://img.shields.io/packagist/dt/dashed/laravel-deepl.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dashed/laravel-deepl.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/dashed/laravel-deepl?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dashed/laravel-deepl
