<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use Wimski\ModelRepositories\Tests\Integration\AbstractIntegrationTestCase;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Repositories\ModelWithRepositoryRepository;

class ModelRepositoryTest extends AbstractIntegrationTestCase
{
    protected ModelWithRepositoryRepository $repository;

    /**
     * @var Collection<int, ModelWithRepository>
     */
    protected Collection $models;

    protected ModelWithRepository $findModel;
    protected ModelWithRepository $firstModel;

    protected function setUp(): void
    {
        parent::setUp();

        ModelWithRepository::factory()->createMany([
            [
                'id'  => 23,
                'foo' => 'lorem',
                'bar' => 'ipsum',
            ],
            [
                'id'  => 36,
                'foo' => 'lorem',
                'bar' => 'sit',
            ],
            [
                'id'  => 51,
                'foo' => 'amet',
                'bar' => 'consectetur',
            ],
        ]);

        /*
         *  [
         *      [
         *          'id'  => 51,
         *          'foo' => 'amet',
         *          'bar' => 'consectetur',
         *      ],
         *      [
         *          'id'  => 23,
         *          'foo' => 'lorem',
         *          'bar' => 'ipsum',
         *      ],
         *      [
         *          'id'  => 36,
         *          'foo' => 'lorem',
         *          'bar' => 'sit',
         *      ],
         * ]
         */
        $this->models = ModelWithRepository::all();

        /** @var ModelWithRepository $findModel */
        $findModel = ModelWithRepository::query()->findOrFail(23);
        $this->findModel = $findModel;

        /** @var ModelWithRepository $firstModel */
        $firstModel       = $this->models->get(0);
        $this->firstModel = $firstModel;

        $this->repository = new ModelWithRepositoryRepository(new ModelWithRepository());
    }

    #[Test]
    public function it_returns_an_eloquent_builder(): void
    {
        $builder = $this->repository->builder();

        self::assertSame(ModelWithRepository::class, get_class($builder->getModel()));
    }

    #[Test]
    #[Depends('it_returns_an_eloquent_builder')]
    public function it_returns_an_eloquent_builder_without_global_scopes(): void
    {
        $builder = $this->repository->builder(false);

        self::assertSame([
            23,
            36,
            51,
        ], $builder->get()->pluck('id')->values()->all());
    }

    #[Test]
    public function it_returns_a_model_for_find(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->find(23);

        self::assertTrue($this->findModel->is($result));

        self::assertSame(23, $result->id);
        self::assertSame('lorem', $result->foo);
        self::assertSame('ipsum', $result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_find')]
    public function it_returns_a_model_with_a_specific_column_for_find(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->find(23, 'id');

        self::assertSame(23, $result->id);
        self::assertNull($result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_find')]
    public function it_returns_a_model_with_specific_columns_for_find(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->find(23, 'id', 'foo');

        self::assertSame(23, $result->id);
        self::assertSame('lorem', $result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    public function it_returns_null_for_find_when_a_model_cannot_be_found(): void
    {
        $result = $this->repository->find(88);

        self::assertNull($result);
    }

    #[Test]
    public function it_returns_a_model_for_find_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrFail(23);

        self::assertTrue($this->findModel->is($result));

        self::assertSame(23, $result->id);
        self::assertSame('lorem', $result->foo);
        self::assertSame('ipsum', $result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_find_or_fail')]
    public function it_returns_a_model_with_a_specific_column_for_find_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrFail(23, 'id');

        self::assertSame(23, $result->id);
        self::assertNull($result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_find_or_fail')]
    public function it_returns_a_model_with_specific_columns_for_find_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrFail(23, 'id', 'foo');

        self::assertSame(23, $result->id);
        self::assertSame('lorem', $result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    public function it_throws_an_exception_for_find_or_fail_when_a_model_cannot_be_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository]');

        $this->repository->findOrFail(88);
    }

    #[Test]
    public function it_returns_a_collection_for_find_many(): void
    {
        $result = $this->repository->findMany([23, 36, 51]);

        $this->assertSameModels($this->models, $result);

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsHaveColumn($result, 'bar');
    }

    #[Test]
    #[Depends('it_returns_a_collection_for_find_many')]
    public function it_returns_a_collection_with_a_specific_column_for_find_many(): void
    {
        $result = $this->repository->findMany([23, 36, 51], 'id');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsDoNotHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    #[Test]
    #[Depends('it_returns_a_collection_for_find_many')]
    public function it_returns_a_collection_with_specific_columns_for_find_many(): void
    {
        $result = $this->repository->findMany([23, 36, 51], 'id', 'foo');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    #[Test]
    public function it_returns_a_model_for_first(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->first();

        self::assertTrue($this->firstModel->is($result));

        self::assertSame(51, $result->id);
        self::assertSame('amet', $result->foo);
        self::assertSame('consectetur', $result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_first')]
    public function it_returns_a_model_with_a_specific_column_for_first(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->first('id');

        self::assertSame(51, $result->id);
        self::assertNull($result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_first')]
    public function it_returns_a_model_with_specific_columns_for_first(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->first('id', 'foo');

        self::assertSame(51, $result->id);
        self::assertSame('amet', $result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    public function it_returns_null_for_first_when_a_model_cannot_be_found(): void
    {
        ModelWithRepository::query()->delete();

        $result = $this->repository->first();

        self::assertNull($result);
    }

    #[Test]
    public function it_returns_a_model_for_first_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstOrFail();

        self::assertTrue($this->firstModel->is($result));

        self::assertSame(51, $result->id);
        self::assertSame('amet', $result->foo);
        self::assertSame('consectetur', $result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_first_or_fail')]
    public function it_returns_a_model_with_a_specific_column_for_first_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstOrFail('id');

        self::assertSame(51, $result->id);
        self::assertNull($result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_first_or_fail')]
    public function it_returns_a_model_with_specific_columns_for_first_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstOrFail('id', 'foo');

        self::assertSame(51, $result->id);
        self::assertSame('amet', $result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    public function it_throws_an_exception_for_first_or_fail_when_a_model_cannot_be_found(): void
    {
        ModelWithRepository::query()->delete();

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository]');

        $this->repository->firstOrFail();
    }

    #[Test]
    public function it_returns_a_model_for_first_where(): void
    {
        $result = $this->repository->firstWhere('foo', 'lorem');

        self::assertTrue($this->findModel->is($result));

        $result = $this->repository->firstWhere([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        self::assertTrue($this->findModel->is($result));
    }

    #[Test]
    public function it_returns_a_collection_for_where_in(): void
    {
        $result = $this->repository->whereIn('id', [23, 36]);

        self::assertSame([
            23,
            36,
        ], $result->pluck('id')->values()->all());
    }

    #[Test]
    public function it_returns_a_collection_for_where_not_in(): void
    {
        $result = $this->repository->whereNotIn('id', [23, 36]);

        self::assertSame([
            51,
        ], $result->pluck('id')->values()->all());
    }

    #[Test]
    public function it_returns_null_for_first_where_when_a_model_cannot_be_found(): void
    {
        $result = $this->repository->firstWhere('foo', 'something');

        self::assertNull($result);
    }

    #[Test]
    public function it_returns_a_model_for_first_where_or_fail(): void
    {
        $result = $this->repository->firstWhereOrFail('foo', 'lorem');

        self::assertTrue($this->findModel->is($result));

        $result = $this->repository->firstWhereOrFail([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        self::assertTrue($this->findModel->is($result));
    }

    #[Test]
    public function it_throws_an_exception_for_first_where_or_fail_when_a_model_cannot_be_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository]');

        $this->repository->firstWhereOrFail('foo', 'something');
    }

    #[Test]
    public function it_returns_a_collection_for_where(): void
    {
        $result = $this->repository->where('foo', 'lorem');

        self::assertSame([
            23,
            36,
        ], $result->pluck('id')->values()->all());

        $result = $this->repository->where([
            'foo' => 'lorem',
        ]);

        self::assertSame([
            23,
            36,
        ], $result->pluck('id')->values()->all());
    }

    #[Test]
    public function it_returns_a_lazy_collection_for_cursor(): void
    {
        $result = $this->repository->cursor();

        self::assertCount(3, $result);

        $result1 = $result->get(0);
        self::assertInstanceOf(ModelWithRepository::class, $result1);
        self::assertSame(51, $result1->getKey());

        $result2 = $result->get(1);
        self::assertInstanceOf(ModelWithRepository::class, $result2);
        self::assertSame(23, $result2->getKey());

        $result3 = $result->get(2);
        self::assertInstanceOf(ModelWithRepository::class, $result3);
        self::assertSame(36, $result3->getKey());
    }

    #[Test]
    public function it_returns_a_collection_for_all(): void
    {
        $result = $this->repository->all();

        $this->assertSameModels($this->models, $result);

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsHaveColumn($result, 'bar');
    }

    #[Test]
    #[Depends('it_returns_a_collection_for_all')]
    public function it_returns_a_collection_with_a_specific_column_for_all(): void
    {
        $result = $this->repository->all('id');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsDoNotHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    #[Test]
    #[Depends('it_returns_a_collection_for_all')]
    public function it_returns_a_collection_with_specific_columns_for_all(): void
    {
        $result = $this->repository->all('id', 'foo');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    #[Test]
    public function it_returns_a_new_model_for_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->make(['foo' => 'bar']);

        self::assertFalse($result->exists);
        self::assertSame('bar', $result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    public function it_returns_a_model_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(23);

        self::assertTrue($this->findModel->is($result));

        self::assertSame(23, $result->id);
        self::assertSame('lorem', $result->foo);
        self::assertSame('ipsum', $result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_find_or_make')]
    public function it_returns_a_model_with_a_specific_column_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(23, 'id');

        self::assertSame(23, $result->id);
        self::assertNull($result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    #[Depends('it_returns_a_model_for_find_or_make')]
    public function it_returns_a_model_with_specific_columns_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(23, 'id', 'foo');

        self::assertSame(23, $result->id);
        self::assertSame('lorem', $result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    public function it_returns_a_new_model_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(88);

        self::assertFalse($result->exists);
        self::assertNull($result->foo);
        self::assertNull($result->bar);
    }

    #[Test]
    public function it_returns_a_model_for_first_where_or_make(): void
    {
        $result = $this->repository->firstWhereOrMake([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        self::assertTrue($this->findModel->is($result));
    }

    #[Test]
    public function it_returns_a_new_model_for_first_where_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstWhereOrMake(
            ['foo' => 'something'],
            ['bar' => 'stuff'],
        );

        self::assertFalse($result->exists);
        self::assertSame('something', $result->foo);
        self::assertSame('stuff', $result->bar);
    }

    #[Test]
    public function it_returns_a_new_model_for_create(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->create([
            'foo' => 'bar',
            'bar' => 'foo',
        ]);

        self::assertTrue($result->exists);
        self::assertSame('bar', $result->foo);
        self::assertSame('foo', $result->bar);

        $this->assertDatabaseCount('model_with_repositories', 4);
    }

    #[Test]
    public function it_returns_a_model_for_first_where_or_create(): void
    {
        $result = $this->repository->firstWhereOrCreate([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        self::assertTrue($this->findModel->is($result));
    }

    #[Test]
    public function it_returns_a_new_model_for_first_where_or_create(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstWhereOrCreate(
            ['foo' => 'something'],
            ['bar' => 'stuff'],
        );

        self::assertTrue($result->exists);
        self::assertSame('something', $result->foo);
        self::assertSame('stuff', $result->bar);

        $this->assertDatabaseCount('model_with_repositories', 4);
    }

    /**
     * @param Collection<int, ModelWithRepository> $expected
     * @param Collection<int, ModelWithRepository> $actual
     */
    protected function assertSameModels(Collection $expected, Collection $actual): void
    {
        self::assertSameSize($expected, $actual);

        foreach ($expected as $index => $expectedModel) {
            /** @var Model $actualModel */
            $actualModel = $actual->get($index);

            self::assertTrue($actualModel->is($expectedModel));
        }
    }

    /**
     * @param Collection<int, ModelWithRepository> $models
     * @param string                          $column
     */
    protected function assertModelsHaveColumn(Collection $models, string $column): void
    {
        self::assertEmpty(
            $models->pluck($column)->filter(function ($value): bool {
                return $value === null;
            })->values()->all(),
            "The models should have column {$column}",
        );
    }

    /**
     * @param Collection<int, ModelWithRepository> $models
     * @param string                          $column
     */
    protected function assertModelsDoNotHaveColumn(Collection $models, string $column): void
    {
        self::assertEmpty(
            $models->pluck($column)->filter(function ($value): bool {
                return $value !== null;
            })->values()->all(),
            "The models should not have column {$column}",
        );
    }
}
