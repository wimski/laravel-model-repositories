<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wimski\ModelRepositories\Contracts\Repositories\ModelRepositoryInterface;

/**
 * @template T of \Illuminate\Database\Eloquent\Model
 * @implements ModelRepositoryInterface<T>
 */
abstract class AbstractModelRepository implements ModelRepositoryInterface
{
    /**
     * @var T
     */
    protected $model;

    public function findOrFail($id)
    {
        /** @var T $model */
        $model = $this->model->findOrFail($id);

        return $model;
    }

    public function findMany(array $ids): Collection
    {
        /** @var Collection<T> $models */
        $models = $this->model->findMany($ids);

        return $models;
    }

    public function all($columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }
}
