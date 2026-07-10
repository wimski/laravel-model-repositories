<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * @template TModel of Model
 * @implements Scope<TModel>
 */
class GlobalScope implements Scope
{
    /**
     * @param  Builder<covariant TModel> $builder
     * @param  TModel          $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder
            ->orderBy('bar')
            ->orderBy('foo')
            ->orderBy('id');
    }
}
