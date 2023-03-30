<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\LazyCollection;
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

    public function builder(bool $withGlobalScopes = true)
    {
        /** @var Builder<T> $builder */
        $builder = $this->model->newQueryWithoutScopes();

        if ($withGlobalScopes) {
            $this->model->registerGlobalScopes($builder);
        }

        /** @var Builder<T>|T $builder */
        return $builder;
    }

    public function find($key, string ...$column)
    {
        /** @var T|null $model */
        $model = $this->builder()->find($key, $this->parseColumns(...$column));

        return $model;
    }

    public function findOrFail($key, string ...$column)
    {
        /** @var T $model */
        $model = $this->builder()->findOrFail($key, $this->parseColumns(...$column));

        return $model;
    }

    public function findMany($keys, string ...$column): Collection
    {
        /** @var Collection<int, T> $models */
        $models = $this->builder()->findMany($keys, $this->parseColumns(...$column));

        return $models;
    }

    public function first(string ...$column)
    {
        /** @var T|null $model */
        $model = $this->builder()->first($this->parseColumns(...$column));

        return $model;
    }

    public function firstOrFail(string ...$column)
    {
        /** @var T $model */
        $model = $this->builder()->firstOrFail($this->parseColumns(...$column));

        return $model;
    }

    public function firstWhere($column, $operator = null, $value = null, string $boolean = 'and')
    {
        /** @var T|null $model */
        $model = $this->builder()->firstWhere($column, $operator, $value, $boolean);

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
        $models = $this->builder()->where($column, $operator, $value, $boolean)->get();

        return $models;
    }

    public function whereIn(string $column, array $values): Collection
    {
        /** @var Collection<int, T> $models */
        $models = $this->builder()->whereIn($column, $values)->get();

        return $models;
    }

    /**
     * @param string  $column
     * @param mixed[] $values
     * @return Collection<int, T>
     */
    public function whereNotIn(string $column, array $values): Collection
    {
        /** @var Collection<int, T> $models */
        $models = $this->builder()->whereNotIn($column, $values)->get();

        return $models;
    }

    public function cursor(): LazyCollection
    {
        /** @var LazyCollection<int, T> $models */
        $models = $this->builder()->cursor();

        return $models;
    }

    public function all(string ...$column): Collection
    {
        /** @var Collection<int, T> $models */
        $models = $this->builder()->get($this->parseColumns(...$column));

        return $models;
    }

    public function make(array $attributes)
    {
        /** @var T $model */
        $model = $this->builder()->make($attributes);

        return $model;
    }

    public function findOrMake($key, string ...$column)
    {
        /** @var T $model */
        $model = $this->builder()->findOrNew($key, $this->parseColumns(...$column));

        return $model;
    }

    public function firstWhereOrMake(array $attributes, array $values = [])
    {
        /** @var T $model */
        $model = $this->builder()->firstOrNew($attributes, $values);

        return $model;
    }

    public function create(array $attributes)
    {
        /** @var T $model */
        $model = $this->builder()->create($attributes);

        return $model;
    }

    public function firstWhereOrCreate(array $attributes, array $values = [])
    {
        /** @var T $model */
        $model = $this->builder()->firstOrCreate($attributes, $values);

        return $model;
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
