<?php

namespace Wimski\ModelRepositories\Tests\Laravel\App\Contracts\Repositories;

use Wimski\ModelRepositories\Contracts\Repositories\ModelRepositoryInterface;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;

/**
 * @extends ModelRepositoryInterface<ModelWithRepository>
 */
interface ModelWithRepositoryRepositoryInterface extends ModelRepositoryInterface
{
}
