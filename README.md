[![Latest Stable Version](http://poser.pugx.org/wimski/laravel-model-repositories/v)](https://packagist.org/packages/wimski/laravel-model-repositories)
[![Coverage Status](https://coveralls.io/repos/github/wimski/laravel-model-repositories/badge.svg?branch=master)](https://coveralls.io/github/wimski/laravel-model-repositories?branch=master)
[![PHPUnit](https://github.com/wimski/laravel-model-repositories/actions/workflows/phpunit.yml/badge.svg)](https://github.com/wimski/laravel-model-repositories/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/wimski/laravel-model-repositories/actions/workflows/phpstan.yml/badge.svg)](https://github.com/wimski/laravel-model-repositories/actions/workflows/phpstan.yml)

# Laravel Model Repositories

Generic repository pattern for Laravel Eloquent models.

* [Changelog](#changelog)
* [Usage](#usage)
  * [Install package](#install-package)
  * [Setup service provider](#setup-service-provider)
  * [Generate a repository](#generate-a-repository)
  * [Add repository binding](#add-repository-binding)
* [Example usage](#example-usage)
* [Namespace configuration](#namespace-configuration)
  * [Assumptions](#assumptions)
  * [Default configuration](#default-configuration)
  * [Multiple configurations](#multiple-configurations)
  * [Specificity](#specificity)
* [Available methods](#available-methods)
* [Stub customization](#stub-customization)

## Changelog

[View the changelog.](./CHANGELOG.md)

## Usage

### Install package

```bash
composer require wimski/laravel-model-repositories
```

### Setup service provider

This package is **NOT** loaded using [package discovery](https://laravel.com/docs/8.x/packages#package-discovery).
You should create your own service provider that extends the one from this package and add it to your config manually.

`app/Providers/RepositoryServiceProvider.php`
```php
<?php

namespace App\Providers;

use Wimski\ModelRepositories\Providers\ModelRepositoryServiceProvider;

class RepositoryServiceProvider extends ModelRepositoryServiceProvider
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
         App\Providers\RepositoryServiceProvider::class,
    ],
];
```

### Generate a repository

```bash
php artisan make:repository App\\Models\\MyModel
```

This will create the following files:
* `MyModelRepositoryInterface.php`
* `MyModelRepository.php`

The namespace and file location depend on the [namespace configuration](#namespace-configuration).

Alternatively you can completely override the contract and repository FQN by using the command options.
The namespace matching and class name suffixing will be skipped.

```bash
php artisan make:repository App\\Models\\MyModel --contract=Foo\\Bar --repository=Lorem\\Ipsum
```

### Add repository binding

Set up the binding of your new repository in the [service provider](#setup-service-provider).

```php
protected array $repositories = [
    MyModelRespositoryInterface::class => MyModelRespository::class,
];
```

## Example usage

```php
class SomeService
{
    protected MyModelRespositoryInterface $repository;
    
    public function __construct(MyModelRespositoryInterface $repository)
    {
        $this->repository = $repository;    
    }
    
    public function doSomething($id): void
    {
        $myModel = $this->repository->findOrFail($id);
    }
}
```

## Namespace configuration

The namespaces configuration is used to determine what the namespaces of your repository classes - and locations of the subsequent files - should be.
Each configuration exists of three parts:
* `models`
* `contracts`
* `repositories`

When [generating a repository](#generate-a-repository) for a model the command will look for a namespace configuration which has a `models` part that matches the supplied model.
The `contracts` and `repositories` parts of that configuration are then used as the namespaces for the repository classes.

### Assumptions

* The namespaces follow the PSR-4 file location convention.
* The namespaces are within the Laravel app directory.

### Default configuration

```php
'namespaces' => [
    [
        'models'       => 'App\\Models',
        'contracts'    => 'App\\Contracts\\Repositories',
        'repositories' => 'App\\Repositories',
    ],
],
```

When generating a repository for `App\Models\MyModel`, the following two classes will be created:
* `App\Contracts\Repositories\MyModelRepositoryInterface`
* `App\Repositories\MyModelRepository`

### Multiple configurations

You can have multiple namespace configurations, for example when using domains.

```php
'namespaces' => [
    [
        'models'       => 'App\\DomainA\\Models',
        'contracts'    => 'App\\DomainA\\Contracts\\Repositories',
        'repositories' => 'App\\DomainA\\Repositories',
    ],
    [
        'models'       => 'App\\DomainB\\Models',
        'contracts'    => 'App\\DomainB\\Contracts\\Repositories',
        'repositories' => 'App\\DomainB\\Repositories',
    ],
],
```

### Specificity

The first match will be used so if you have overlapping namespace configurations, make sure to have the more specific ones on top.

```php
'namespaces' => [
    [
        'models' => 'App\\Models\\SpecificModels',
        ...
    ],
    [
        'models' => 'App\\Models',
        ...
    ],
],
```

## Available methods

* `findOrFail`
* `findMany`
* `all`

## Stub customization

See Laravel's documentation about [stub customization](https://laravel.com/docs/8.x/artisan#stub-customization).
This package adds the following stub files:
* `model.repository.interface.stub`
* `model.repository.stub`

## PHPUnit

```bash
composer test
```

## PHPStan

```bash
composer analyze
```

## Credits

- [wimski](https://github.com/wimski)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
