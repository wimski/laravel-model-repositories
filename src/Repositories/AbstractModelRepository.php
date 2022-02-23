<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function find($key, string ...$column)
    {
        /** @var T|null $model */
        $model = $this->model->find($key, $this->parseColumns(...$column));

        return $model;
    }

    public function findOrFail($key, string ...$column)
    {
        /** @var T $model */
        $model = $this->model->findOrFail($key, $this->parseColumns(...$column));

        return $model;
    }

    public function findMany($keys, string ...$column): Collection
    {
        /** @var Collection<int, T> $models */
        $models = $this->model->findMany($keys, $this->parseColumns(...$column));

        return $models;
    }

    public function firstWhere($column, $operator = null, $value = null, string $boolean = 'and')
    {
        /** @var T|null $model */
        $model = $this->model->firstWhere($column, $operator, $value, $boolean);

        return $model;
    }

    public function firstWhereOrFail($column, $operator = null, $value = null, string $boolean = 'and')
    {
        $model = $this->firstWhere($column, $operator, $value, $boolean);

        if ($model === null) {
            $this->throwModelNotFoundException();
        }

        /** @var T $model */
        return $model;
    }

    public function where($column, $operator = null, $value = null, string $boolean = 'and'): Collection
    {
        /** @var Collection<int, T> $models */
        $models = $this->model->where($column, $operator, $value, $boolean)->get();

        return $models;
    }

    public function all(string ...$column): Collection
    {
        /** @var Collection<int, T> $models */
        $models = $this->model->all($this->parseColumns(...$column));

        return $models;
    }

    /**
     * @param string ...$column
     * @return string[]
     */
    protected function parseColumns(string ...$column): array
    {
        return empty($column) ? ['*'] : $column;
    }

    /**
     * @param int|string ...$key
     * @throws ModelNotFoundException
     */
    protected function throwModelNotFoundException(...$key): void
    {
        throw (new ModelNotFoundException())->setModel(get_class($this->model), array_values($key));
    }
}
