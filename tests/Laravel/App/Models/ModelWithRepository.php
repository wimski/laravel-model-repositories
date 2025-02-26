<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wimski\ModelRepositories\Tests\Laravel\App\Scopes\GlobalScope;
use Wimski\ModelRepositories\Tests\Laravel\Database\Factories\ModelWithRepositoryFactory;

/**
 * @property int $id
 * @property string|null $foo
 * @property string|null $bar
 */
class ModelWithRepository extends Model
{
    /**
     * @use HasFactory<ModelWithRepositoryFactory>
     */
    use HasFactory;

    protected $fillable = [
        'foo',
        'bar',
    ];

    protected static function booted(): void
    {
        self::addGlobalScope(new GlobalScope());
    }

    protected static function newFactory(): ModelWithRepositoryFactory
    {
        return new ModelWithRepositoryFactory();
    }
}
