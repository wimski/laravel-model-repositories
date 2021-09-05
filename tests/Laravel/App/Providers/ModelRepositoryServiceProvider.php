<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Providers;

use Wimski\ModelRepositories\Providers\ModelRepositoryServiceProvider as ServiceProvider;
use Wimski\ModelRepositories\Tests\Laravel\App\Contracts\Repositories\ModelWithRepositoryRepositoryInterface;
use Wimski\ModelRepositories\Tests\Laravel\App\Repositories\ModelWithRepositoryRepository;

class ModelRepositoryServiceProvider extends ServiceProvider
{
    protected array $repositories = [
        ModelWithRepositoryRepositoryInterface::class => ModelWithRepositoryRepository::class,
    ];
}
