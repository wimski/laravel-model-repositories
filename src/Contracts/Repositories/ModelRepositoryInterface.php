<?php

namespace Wimski\ModelRepositories\Contracts\Repositories;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\LazyCollection;

/**
 * @template TModel of Model
 */
interface ModelRepositoryInterface
{
    /**
     * @return Builder<TModel>
     */
    public function builder(bool $withGlobalScopes = true): Builder;

    /**
     * @return TModel|null
     */
    public function find(int|string $key, string ...$column): ?Model;

    /**
     * @return TModel
     * @throws ModelNotFoundException
     */
    public function findOrFail(int|string $key, string ...$column): Model;

    /**
     * @param Arrayable<array-key, int|string>|array<array-key, int|string> $keys
     * @return Collection<int, TModel>
     */
    public function findMany(Arrayable|array $keys, string ...$column): Collection;

    /**
     * @return TModel|null
     */
    public function first(string ...$column): ?Model;

    /**
     * @return TModel
     * @throws ModelNotFoundException
     */
    public function firstOrFail(string ...$column): Model;

    /**
     * @param string|array<int|string, mixed>|Closure(Builder<TModel>):Builder<TModel>|Expression<float|int|string> $column
     * @return TModel|null
     */
    public function firstWhere(string|array|Closure|Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): ?Model;

    /**
     * @param string|array<int|string, mixed>|Closure(Builder<TModel>):Builder<TModel>|Expression<float|int|string> $column
     * @return TModel
     * @throws ModelNotFoundException
     */
    public function firstWhereOrFail(string|array|Closure|Expression$column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): Model;

    /**
     * @param string|array<int|string, mixed>|Closure(Builder<TModel>):Builder<TModel>|Expression<float|int|string> $column
     * @return Collection<int, TModel>
     */
    public function where(string|array|Closure|Expression$column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): Collection;

    /**
     * @param array<array-key, mixed> $values
     * @return Collection<int, TModel>
     */
    public function whereIn(string $column, array $values): Collection;

    /**
     * @param array<array-key, mixed> $values
     * @return Collection<int, TModel>
     */
    public function whereNotIn(string $column, array $values): Collection;

    /**
     * @return LazyCollection<int, TModel>
     */
    public function cursor(): LazyCollection;

    /**
     * @return Collection<int, TModel>
     */
    public function all(string ...$column): Collection;

    /**
     * @param array<string, mixed> $attributes
     * @return TModel
     */
    public function make(array $attributes): Model;

    /**
     * @return TModel
     */
    public function findOrMake(int|string $key, string ...$column): Model;

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return TModel
     */
    public function firstWhereOrMake(array $attributes, array $values = []): Model;

    /**
     * @param array<string, mixed> $attributes
     * @return TModel
     */
    public function create(array $attributes): Model;

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return TModel
     */
    public function firstWhereOrCreate(array $attributes, array $values = []): Model;
}
