<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @template T of \Illuminate\Database\Eloquent\Model
 */
interface ModelRepositoryInterface
{
    /**
     * @param int|string $id
     * @return T
     * @throws ModelNotFoundException
     */
    public function findOrFail($id);

    /**
     * @param int[]|string[] $ids
     * @return Collection<T>
     */
    public function findMany(array $ids): Collection;

    /**
     * @param string|string[] $columns
     * @return Collection<T>
     */
    public function all($columns = ['*']): Collection;
}
