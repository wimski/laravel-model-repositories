<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GlobalScope implements Scope
{
    /**
     * @template TModel of Model
     * @param  Builder<TModel> $builder
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
