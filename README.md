# PDFreactor

Class updates to the original PDFreactor library and a wrapper for using the library with Laravel 5+.

## Requirements
- PHP 7.0+

## Installation

```
composer require ssgglobal/pdf-reactor
```

### Using with Laravel

Publish the pdfreactor config
```
php artistan vendor:publish
```

Add pdfreactor settings to .env and/or .env.example
```
; PDFreactor
PDFREACTOR_HOST=http://pdfreactor.domain
PDFREACTOR_PORT=9423
PDFREACTOR_KEY=
```

Add Service Provider to config/app.php
```
'providers' => [
    StepStone\PDFreactor\Providers\ServiceProvider::class
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

#### Using an array
```
use StepStone\PDFreactor\PDFreactor;

$pdfreactor = new PDFreactor($url);

// config data
$config = ['document' => file_get_contents('data_to_convert.html')];

$results = $pdfreactor->convertAsync($config);
```

#### Using the Config class
```
use StepStone\PDFreactor\PDFreactor;
use StepStone\PDFreactor\Config as PDFreactorConfig;

$pdfreactor = new PDFreactor($url);

$config = new PDFreactorConfig(file_get_contents('data_to_convert.html'));
$config->keepDocument();

$results = $pdfreactor->convertAsync($config);
```

### Laravel 5.0+
```
use PDFreactor;
use StepStone\PDFreactor\Config as PDFreactorConfig;

$config = new PDFreactorConfig($data);

$results = PDFreactor::convert($config);
```