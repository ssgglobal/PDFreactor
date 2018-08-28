# PDFreactor

Class updates to the original PDFreactor library and a wrapper for using the library with Laravel 5+.

## Requirements
- PHP 7.0+

## Installation

```
composer require ssgglobal/pdf-reactor
```

### Using with Laravel

Add Service Provider to config/app.php
```
'providers' => [
    StepStone\PDFreactor\PDFreactorServiceProvider::class
],
```

Add the PDFreactor Facade to the Alias list
```
'aliases' => [
    'PDFreactor` => StepStone\PDFreactor\Facades\PDFreactor::class,
],
```

## Usage

### Vanilla PHP
```
use StepStone\PDFreactor\PDFreactor;

$url    = 'http://pdfreactor.domain';
$port   = 999;

$pdfreactor = new PDFreactor($url, $port);
$results = $pdfreactor->convert($config);
```

### Laravel 5.0+
```
use PDFreactor;

$results = PDFreactor::convert($config);
```