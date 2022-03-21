<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wimski\ModelRepositories\Tests\Laravel\Database\Factories\ModelWithRepositoryFactory;

/**
 * @property int $id
 * @property string|null $foo
 * @property string|null $bar
 * @method static \Wimski\ModelRepositories\Tests\Laravel\Database\Factories\ModelWithRepositoryFactory factory(...$parameters)
 */
class ModelWithRepository extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'foo',
        'bar',
    ];

    /**
     * @return Factory<ModelWithRepository>
     */
    protected static function newFactory(): Factory
    {
        return new ModelWithRepositoryFactory();
    }
}
