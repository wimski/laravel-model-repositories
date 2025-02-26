<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Repositories;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\LazyCollection;
use Wimski\ModelRepositories\Contracts\Repositories\ModelRepositoryInterface;

/**
 * @template TModel of Model
 * @implements ModelRepositoryInterface<TModel>
 */
abstract readonly class AbstractModelRepository implements ModelRepositoryInterface
{
    /**
     * @param TModel $model
     */
    public function __construct(
        protected Model $model,
    ) {
    }

    public function builder(bool $withGlobalScopes = true): Builder
    {
        $builder = $this->model->newQueryWithoutScopes();

        if ($withGlobalScopes) {
            $this->model->registerGlobalScopes($builder);
        }

        return $builder;
    }

    public function find(int|string $key, string ...$column): ?Model
    {
        return $this->builder()->find($key, $this->parseColumns(...$column));
    }

    public function findOrFail(int|string $key, string ...$column): Model
    {
        return $this->builder()->findOrFail($key, $this->parseColumns(...$column));
    }

    public function findMany(Arrayable|array $keys, string ...$column): Collection
    {
        /** @var Arrayable<array-key, mixed>|array<array-key, int|string> $keys */
        return $this->builder()->findMany($keys, $this->parseColumns(...$column));
    }

    public function first(string ...$column): ?Model
    {
        return $this->builder()->first($this->parseColumns(...$column));
    }

    public function firstOrFail(string ...$column): Model
    {
        return $this->builder()->firstOrFail($this->parseColumns(...$column));
    }

    public function firstWhere(string|array|Closure|Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): ?Model
    {
        return $this->builder()->firstWhere($column, $operator, $value, $boolean);
    }

    public function firstWhereOrFail(string|array|Closure|Expression$column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): Model
    {
        $model = $this->firstWhere($column, $operator, $value, $boolean);

        if ($model === null) {
            throw $this->makeModelNotFoundException();
        }

        return $model;
    }

    public function where(string|array|Closure|Expression$column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): Collection
    {
        return $this->builder()->where($column, $operator, $value, $boolean)->get();
    }

    public function whereIn(string $column, array $values): Collection
    {
        return $this->builder()->whereIn($column, $values)->get();
    }

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

    public function make(array $attributes): Model
    {
        return $this->builder()->make($attributes);
    }

    public function findOrMake(int|string $key, string ...$column): Model
    {
        return $this->builder()->findOrNew($key, $this->parseColumns(...$column));
    }

    public function firstWhereOrMake(array $attributes, array $values = []): Model
    {
        return $this->builder()->firstOrNew($attributes, $values);
    }

    public function create(array $attributes): Model
    {
        return $this->builder()->create($attributes);
    }

    public function firstWhereOrCreate(array $attributes, array $values = []): Model
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
    protected function throwModelNotFoundException(int|string ...$key): void
    {
        throw $this->makeModelNotFoundException(...$key);
    }

    /**
     * @return ModelNotFoundException<TModel>
     */
    protected function makeModelNotFoundException(int|string ...$key): ModelNotFoundException
    {
        /** @var ModelNotFoundException<TModel> $exception */
        $exception = (new ModelNotFoundException())->setModel(get_class($this->model), array_values($key));

        return $exception;
    }
}
