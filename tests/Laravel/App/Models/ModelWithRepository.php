<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wimski\ModelRepositories\Tests\Laravel\Database\Factories\ModelWithRepositoryFactory;

/**
 * @method static \Wimski\ModelRepositories\Tests\Laravel\Database\Factories\ModelWithRepositoryFactory factory(...$parameters)
 */
class ModelWithRepository extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return new ModelWithRepositoryFactory();
    }
}
