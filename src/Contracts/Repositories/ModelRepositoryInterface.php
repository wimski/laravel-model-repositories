<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Contracts\Repositories;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Expression;

/**
 * @template T of \Illuminate\Database\Eloquent\Model
 */
interface ModelRepositoryInterface
{
    /**
     * @param int|string $key
     * @param string     ...$column
     * @return T|null
     */
    public function find($key, string ...$column);

    /**
     * @param int|string $key
     * @param string     ...$column
     * @return T
     * @throws ModelNotFoundException
     */
    public function findOrFail($key, string ...$column);

    /**
     * @param int[]|string[]|Arrayable $keys
     * @param string                   ...$column
     * @return Collection<T>
     */
    public function findMany($keys, string ...$column): Collection;

    /**
     * @param string|string[]|array<string, mixed>|Closure|Expression $column
     * @param mixed                                                   $operator
     * @param mixed                                                   $value
     * @param string                                                  $boolean
     * @return T|null
     */
    public function firstWhere($column, $operator = null, $value = null, string $boolean = 'and');

    /**
     * @param string|string[]|array<string, mixed>|Closure|Expression $column
     * @param mixed                                                   $operator
     * @param mixed                                                   $value
     * @param string                                                  $boolean
     * @return T
     * @throws ModelNotFoundException
     */
    public function firstWhereOrFail($column, $operator = null, $value = null, string $boolean = 'and');

    /**
     * @param string|string[]|array<string, mixed>|Closure|Expression $column
     * @param mixed                                                   $operator
     * @param mixed                                                   $value
     * @param string                                                  $boolean
     * @return Collection<T>
     */
    public function where($column, $operator = null, $value = null, string $boolean = 'and'): Collection;

    /**
     *
     * @param string ...$column
     * @return Collection<T>
     */
    public function all(string ...$column): Collection;
}
