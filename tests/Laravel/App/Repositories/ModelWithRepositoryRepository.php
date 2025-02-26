<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Repositories;

use Wimski\ModelRepositories\Repositories\AbstractModelRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Contracts\Repositories\ModelWithRepositoryRepositoryInterface;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;

/**
 * @extends AbstractModelRepository<ModelWithRepository>
 */
readonly class ModelWithRepositoryRepository extends AbstractModelRepository implements ModelWithRepositoryRepositoryInterface
{
    public function __construct(ModelWithRepository $model)
    {
        parent::__construct($model);
    }
}
