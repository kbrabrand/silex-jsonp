JSONP provider for Silex
===========
Content type aware JSON response handler for Silex

[![Build Status](https://travis-ci.org/kbrabrand/silex-jsonp.svg?branch=master)](https://travis-ci.org/kbrabrand/silex-jsonp)

## Installation
Add `"kbrabrand/silex-jsonp": "XXX"` to the composer.json file inside your project and do a `composer install`. Check [Composer][1] for the latest available version.

## Setup instructions
Register the JSONP service provider in your Silex app like this;

```php
use KBrabrand\Silex\Provider\JSONPServiceProvider;

$app->register(new JSONPServiceProvider(), array(
    'JSONP.callback'     => 'cb',                 // GET parameter containing the callback method name (optional)
    'JSONP.contentTypes' => ['application/json'], // List of response content types to use with JSONP (optional)
));
```

## Usage
After registering the JSONP service provider an after hook will be added and the response content will be modified before it's returned to the user if the URL contains a callback and the content type of the response is in the list of allowed content types.

## Tests
The service provider comes with PHPUnit tests and can be run by doing a `./vendor/phpunit/phpunit/phpunit` inside the silex-jsonp folder.

## License
License
Copyright (c) 2015, Kristoffer Brabrand kristoffer@brabrand.no

Licensed under the MIT License

[1]: http://packagist.org/packages/kbrabrand/silex-jsonp
