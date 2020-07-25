# PDFreactor

A Class for working with a [PDFreactor](https://www.pdfreactor.com/) service.

## Requirements
- PHP 7.0+
- ext-curl
- ext-json

## Installation

### Composer

```
composer require ssgglobal/pdf-reactor
```

## Configuration

The library needs information in order to connect to the PDFreactor REST service.

* API URL - The server host (e.g. http://mypdfreactorserver.com).
* API PORT - Port number the server is listening on (default: 9423)
* API KEY - Your API key to access the service (OPTIONAL).
* ADMIN KEY - A key that is required if you use the server monitor API.

### Laravel

Publish the PDFreactor config

```
php artisan vendor:publish
```

Add pdfreactor settings to .env and/or .env.example

```
PDFREACTOR_HOST=http://mypdfreactorserver.com
PDFREACTOR_PORT=9423
PDFREACTOR_KEY=
PDFREACTOR_ADMIN_KEY=
```

(Optional) Add an Alias for PDFreactor

```
// config/app.php
[
    'aliases' => [
        'PDFreactor'    => StepStone\PDFreactor\Facades\PDFreactor::class,
    ],
]
```

## Usage

### Vanilla PHP

```
use StepStone\PDFreactor\PDFreactor;

$pdfreactor = new PDFreactor($host, $port);
```

#### Converting a document async with a config array

```
$config = [
    'document'  => file_get_contents('data_to_convert.html'),
]

$result = $pdfreactor->convertAsync($config);
```

#### Convert a document async with a Convertable object.

```
use StepStone\PDFreactor\Convertable;

// from a file
$config = Convertable::create('<p>My PDF</p>');
// or read from a file - Convertable::createFromFile('data_to_convert.html');

$result = $pdfreactor->convertAsync($config);
```

### Laravel & Lumen

```
use PDFreactor;
use StepStone\PDFreactor\Convertable;

$config = Convertable::create('<p>My PDF</p>');

$result = PDFreactor::convertAsync($config);
```

### Additional Methods

For more information about available methods look at the [PDFreactor](https://github.com/ssgglobal/PDFreactor/blob/master/src/PDFreactor/PDFreactor.php) and [Monitor](https://github.com/ssgglobal/PDFreactor/blob/master/src/PDFreactor/Monitor.php) Classes.