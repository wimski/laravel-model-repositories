<?php

declare(strict_types=1);

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
     * @param int|string $key
     * @param string     ...$column
     * @return TModel|null
     */
    public function find($key, string ...$column);

    /**
     * @param int|string $key
     * @param string     ...$column
     * @return TModel
     * @throws ModelNotFoundException
     */
    public function findOrFail($key, string ...$column);

    /**
     * @param int[]|string[]|Arrayable<int|string, mixed> $keys
     * @param string                                      ...$column
     * @return Collection<int, TModel>
     */
    public function findMany($keys, string ...$column): Collection;

    /**
     * @param string ...$column
     * @return TModel|null
     */
    public function first(string ...$column);

    /**
     * @param string ...$column
     * @return TModel
     * @throws ModelNotFoundException
     */
    public function firstOrFail(string ...$column);

    /**
     * @param string|mixed[]|Closure|Expression $column
     * @param mixed                             $operator
     * @param mixed                             $value
     * @param string                            $boolean
     * @return TModel|null
     */
    public function firstWhere($column, $operator = null, $value = null, string $boolean = 'and');

    /**
     * @param string|mixed[]|Closure|Expression $column
     * @param mixed                             $operator
     * @param mixed                             $value
     * @param string                            $boolean
     * @return TModel
     * @throws ModelNotFoundException
     */
    public function firstWhereOrFail($column, $operator = null, $value = null, string $boolean = 'and');

    /**
     * @param string|mixed[]|Closure|Expression $column
     * @param mixed                             $operator
     * @param mixed                             $value
     * @param string                            $boolean
     * @return Collection<int, TModel>
     */
    public function where($column, $operator = null, $value = null, string $boolean = 'and'): Collection;

    /**
     * @param string  $column
     * @param mixed[] $values
     * @return Collection<int, TModel>
     */
    public function whereIn(string $column, array $values): Collection;

    /**
     * @param string  $column
     * @param mixed[] $values
     * @return Collection<int, TModel>
     */
    public function whereNotIn(string $column, array $values): Collection;

    /**
     * @return LazyCollection<int, TModel>
     */
    public function cursor(): LazyCollection;

    /**
     *
     * @param string ...$column
     * @return Collection<int, TModel>
     */
    public function all(string ...$column): Collection;

    /**
     * @param array<string, mixed> $attributes
     * @return TModel
     */
    public function make(array $attributes);

    /**
     * @param int|string $key
     * @param string     ...$column
     * @return TModel
     */
    public function findOrMake($key, string ...$column);

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return TModel
     */
    public function firstWhereOrMake(array $attributes, array $values = []);

    /**
     * @param array<string, mixed> $attributes
     * @return TModel
     */
    public function create(array $attributes);

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return TModel
     */
    public function firstWhereOrCreate(array $attributes, array $values = []);
}
