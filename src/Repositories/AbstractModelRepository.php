<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\LazyCollection;
use Wimski\ModelRepositories\Contracts\Repositories\ModelRepositoryInterface;

/**
 * @template TModel of Model
 * @implements ModelRepositoryInterface<TModel>
 */
abstract class AbstractModelRepository implements ModelRepositoryInterface
{
    /**
     * @var TModel
     */
    protected Model $model;

    public function builder(bool $withGlobalScopes = true): Builder
    {
        $builder = $this->model->newQueryWithoutScopes();

        if ($withGlobalScopes) {
            $this->model->registerGlobalScopes($builder);
        }

        return $builder;
    }

    public function find($key, string ...$column)
    {
        return $this->builder()->find($key, $this->parseColumns(...$column));
    }

    public function findOrFail($key, string ...$column)
    {
        return $this->builder()->findOrFail($key, $this->parseColumns(...$column));
    }

    public function findMany($keys, string ...$column): Collection
    {
        return $this->builder()->findMany($keys, $this->parseColumns(...$column));
    }

    public function first(string ...$column)
    {
        return $this->builder()->first($this->parseColumns(...$column));
    }

    public function firstOrFail(string ...$column)
    {
        return $this->builder()->firstOrFail($this->parseColumns(...$column));
    }

    public function firstWhere($column, $operator = null, $value = null, string $boolean = 'and')
    {
        return $this->builder()->firstWhere($column, $operator, $value, $boolean);
    }

    public function firstWhereOrFail($column, $operator = null, $value = null, string $boolean = 'and')
    {
        $model = $this->firstWhere($column, $operator, $value, $boolean);

        if ($model === null) {
            $this->throwModelNotFoundException();
        }

        /** @var TModel $model */
        return $model;
    }

    public function where($column, $operator = null, $value = null, string $boolean = 'and'): Collection
    {
        return $this->builder()->where($column, $operator, $value, $boolean)->get();
    }

    public function whereIn(string $column, array $values): Collection
    {
        return $this->builder()->whereIn($column, $values)->get();
    }

    /**
     * @param string  $column
     * @param mixed[] $values
     * @return Collection<int, TModel>
     */
    public function whereNotIn(string $column, array $values): Collection
    {
        return $this->builder()->whereNotIn($column, $values)->get();
    }

    public function cursor(): LazyCollection
    {
        return $this->builder()->cursor();
    }

    public function all(string ...$column): Collection
    {
        return $this->builder()->get($this->parseColumns(...$column));
    }

    public function make(array $attributes)
    {
        return $this->builder()->make($attributes);
    }

    public function findOrMake($key, string ...$column)
    {
        return $this->builder()->findOrNew($key, $this->parseColumns(...$column));
    }

    public function firstWhereOrMake(array $attributes, array $values = [])
    {
        return $this->builder()->firstOrNew($attributes, $values);
    }

    public function create(array $attributes)
    {
        return $this->builder()->create($attributes);
    }

    public function firstWhereOrCreate(array $attributes, array $values = [])
    {
        return $this->builder()->firstOrCreate($attributes, $values);
    }

    /**
     * @return array<int, string>
     */
    protected function parseColumns(string ...$column): array
    {
        return empty($column) ? ['*'] : array_values($column);
    }

    /**
     * @throws ModelNotFoundException
     */
    protected function throwModelNotFoundException(...$key): void
    {
        throw (new ModelNotFoundException())->setModel(get_class($this->model), array_values($key));
    }
}
