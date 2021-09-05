[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Coverage Status](https://coveralls.io/repos/github/wimski/laravel-model-repositories/badge.svg?branch=master)](https://coveralls.io/github/wimski/laravel-model-repositories?branch=master)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

# Laravel Model Repositories

Generic repository pattern for Laravel Eloquent models.

## Installation

You can install the package via composer:

```bash
composer require --dev wimski/laravel-model-repositories
```

The package is **NOT** loaded using [Package Discovery](https://laravel.com/docs/8.x/packages#package-discovery).
You should create your own service provider that extends the one from this package and it add it to your config manually.

```php
<?php

namespace App\Providers;

use Wimski\ModelRepositories\Providers\ServiceProvider as ServiceProvider;

class ModelRepositoryServiceProvider extends ServiceProvider
{
    protected array $repositories = [
        // add your repository bindings here
    ];
}
```

`config/app.php`
```php
<?php

return [
    'providers' => [
        /*
         * Application Service Providers...
         */
         App\Providers\ModelRepositoryServiceProvider::class,
    ],
];
```

## Usage

* generate repositories
* add to SP
* edit/publish namespaces config
* edit/publish stubs

## Testing

```bash
composer test
```

## Credits

- [wimski](https://github.com/wimski)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
