<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Wimski\ModelRepositories\Tests\Integration\AbstractIntegrationTest;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Repositories\ModelWithRepositoryRepository;

class ModelRepositoryTest extends AbstractIntegrationTest
{
    protected ModelWithRepositoryRepository $repository;

    /**
     * @var Collection<int, ModelWithRepository>
     */
    protected Collection $models;

    protected ModelWithRepository $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->models = ModelWithRepository::factory()->createMany([
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

        $this->model = $this->models->get(0);

        $this->repository = new ModelWithRepositoryRepository(new ModelWithRepository());
    }

    /**
     * @test
     */
    public function it_returns_an_eloquent_builder(): void
    {
        $builder = $this->repository->builder();

        self::assertInstanceOf(Builder::class, $builder);
        self::assertSame(ModelWithRepository::class, get_class($builder->getModel()));
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_find(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->find(23);

        static::assertTrue($this->model->is($result));

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertSame('ipsum', $result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_find
     */
    public function it_returns_a_model_with_a_specific_column_for_find(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->find(23, 'id');

        static::assertSame(23, $result->id);
        static::assertNull($result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_find
     */
    public function it_returns_a_model_with_specific_columns_for_find(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->find(23, 'id', 'foo');

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     */
    public function it_returns_null_for_find_when_a_model_cannot_be_found(): void
    {
        $result = $this->repository->find(88);

        static::assertNull($result);
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_find_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrFail(23);

        static::assertTrue($this->model->is($result));

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertSame('ipsum', $result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_find_or_fail
     */
    public function it_returns_a_model_with_a_specific_column_for_find_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrFail(23, 'id');

        static::assertSame(23, $result->id);
        static::assertNull($result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_find_or_fail
     */
    public function it_returns_a_model_with_specific_columns_for_find_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrFail(23, 'id', 'foo');

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_for_find_or_fail_when_a_model_cannot_be_found(): void
    {
        static::expectException(ModelNotFoundException::class);
        static::expectExceptionMessage('No query results for model [Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository]');

        $this->repository->findOrFail(88);
    }

    /**
     * @test
     */
    public function it_returns_a_collection_for_find_many(): void
    {
        $result = $this->repository->findMany([23, 36, 51]);

        $this->assertSameModels($this->models, $result);

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsHaveColumn($result, 'bar');
    }

    /**
     * @test
     * @depends it_returns_a_collection_for_find_many
     */
    public function it_returns_a_collection_with_a_specific_column_for_find_many(): void
    {
        $result = $this->repository->findMany([23, 36, 51], 'id');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsDoNotHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    /**
     * @test
     * @depends it_returns_a_collection_for_find_many
     */
    public function it_returns_a_collection_with_specific_columns_for_find_many(): void
    {
        $result = $this->repository->findMany([23, 36, 51], 'id', 'foo');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_first(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->first();

        static::assertTrue($this->model->is($result));

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertSame('ipsum', $result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_first
     */
    public function it_returns_a_model_with_a_specific_column_for_first(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->first('id');

        static::assertSame(23, $result->id);
        static::assertNull($result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_first
     */
    public function it_returns_a_model_with_specific_columns_for_first(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->first('id', 'foo');

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     */
    public function it_returns_null_for_first_when_a_model_cannot_be_found(): void
    {
        ModelWithRepository::query()->delete();

        $result = $this->repository->first();

        static::assertNull($result);
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_first_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstOrFail();

        static::assertTrue($this->model->is($result));

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertSame('ipsum', $result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_first_or_fail
     */
    public function it_returns_a_model_with_a_specific_column_for_first_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstOrFail('id');

        static::assertSame(23, $result->id);
        static::assertNull($result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_first_or_fail
     */
    public function it_returns_a_model_with_specific_columns_for_first_or_fail(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstOrFail('id', 'foo');

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_for_first_or_fail_when_a_model_cannot_be_found(): void
    {
        ModelWithRepository::query()->delete();

        static::expectException(ModelNotFoundException::class);
        static::expectExceptionMessage('No query results for model [Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository]');

        $this->repository->firstOrFail();
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_first_where(): void
    {
        $result = $this->repository->firstWhere('foo', 'lorem');

        static::assertTrue($this->model->is($result));

        $result = $this->repository->firstWhere([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        static::assertTrue($this->model->is($result));
    }

    /**
     * @test
     */
    public function it_returns_a_collection_for_where_in(): void
    {
        $result = $this->repository->whereIn('id', [23, 36]);

        static::assertSame([
            23,
            36,
        ], $result->pluck('id')->values()->all());
    }

    /**
     * @test
     */
    public function it_returns_a_collection_for_where_not_in(): void
    {
        $result = $this->repository->whereNotIn('id', [23, 36]);

        static::assertSame([
            51,
        ], $result->pluck('id')->values()->all());
    }

    /**
     * @test
     */
    public function it_returns_null_for_first_where_when_a_model_cannot_be_found(): void
    {
        $result = $this->repository->firstWhere('foo', 'something');

        static::assertNull($result);
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_first_where_or_fail(): void
    {
        $result = $this->repository->firstWhereOrFail('foo', 'lorem');

        static::assertTrue($this->model->is($result));

        $result = $this->repository->firstWhereOrFail([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        static::assertTrue($this->model->is($result));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_for_first_where_or_fail_when_a_model_cannot_be_found(): void
    {
        static::expectException(ModelNotFoundException::class);
        static::expectExceptionMessage('No query results for model [Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository]');

        $this->repository->firstWhereOrFail('foo', 'something');
    }

    /**
     * @test
     */
    public function it_returns_a_collection_for_where(): void
    {
        $result = $this->repository->where('foo', 'lorem');

        static::assertSame([
            23,
            36,
        ], $result->pluck('id')->values()->all());

        $result = $this->repository->where([
            'foo' => 'lorem',
        ]);

        static::assertSame([
            23,
            36,
        ], $result->pluck('id')->values()->all());
    }

    /**
     * @test
     */
    public function it_returns_a_lazy_collection_for_cursor(): void
    {
        $result = $this->repository->cursor();

        static::assertCount(3, $result);

        $result1 = $result->get(0);
        static::assertInstanceOf(ModelWithRepository::class, $result1);
        static::assertSame(23, $result1->getKey());

        $result2 = $result->get(1);
        static::assertInstanceOf(ModelWithRepository::class, $result2);
        static::assertSame(36, $result2->getKey());

        $result3 = $result->get(2);
        static::assertInstanceOf(ModelWithRepository::class, $result3);
        static::assertSame(51, $result3->getKey());
    }

    /**
     * @test
     */
    public function it_returns_a_collection_for_all(): void
    {
        $result = $this->repository->all();

        $this->assertSameModels($this->models, $result);

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsHaveColumn($result, 'bar');
    }

    /**
     * @test
     * @depends it_returns_a_collection_for_all
     */
    public function it_returns_a_collection_with_a_specific_column_for_all(): void
    {
        $result = $this->repository->all('id');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsDoNotHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    /**
     * @test
     * @depends it_returns_a_collection_for_all
     */
    public function it_returns_a_collection_with_specific_columns_for_all(): void
    {
        $result = $this->repository->all('id', 'foo');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    /**
     * @test
     */
    public function it_returns_a_new_model_for_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->make(['foo' => 'bar']);

        static::assertFalse($result->exists);
        static::assertSame('bar', $result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(23);

        static::assertTrue($this->model->is($result));

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertSame('ipsum', $result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_find_or_make
     */
    public function it_returns_a_model_with_a_specific_column_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(23, 'id');

        static::assertSame(23, $result->id);
        static::assertNull($result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     * @depends it_returns_a_model_for_find_or_make
     */
    public function it_returns_a_model_with_specific_columns_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(23, 'id', 'foo');

        static::assertSame(23, $result->id);
        static::assertSame('lorem', $result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     */
    public function it_returns_a_new_model_for_find_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->findOrMake(88);

        static::assertFalse($result->exists);
        static::assertNull($result->foo);
        static::assertNull($result->bar);
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_first_where_or_make(): void
    {
        $result = $this->repository->firstWhereOrMake([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        static::assertTrue($this->model->is($result));
    }

    /**
     * @test
     */
    public function it_returns_a_new_model_for_first_where_or_make(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstWhereOrMake(
            ['foo' => 'something'],
            ['bar' => 'stuff'],
        );

        static::assertFalse($result->exists);
        static::assertSame('something', $result->foo);
        static::assertSame('stuff', $result->bar);
    }

    /**
     * @test
     */
    public function it_returns_a_new_model_for_create(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->create([
            'foo' => 'bar',
            'bar' => 'foo',
        ]);

        static::assertTrue($result->exists);
        static::assertSame('bar', $result->foo);
        static::assertSame('foo', $result->bar);

        $this->assertDatabaseCount('model_with_repositories', 4);
    }

    /**
     * @test
     */
    public function it_returns_a_model_for_first_where_or_create(): void
    {
        $result = $this->repository->firstWhereOrCreate([
            'foo' => 'lorem',
            'bar' => 'ipsum',
        ]);

        static::assertTrue($this->model->is($result));
    }

    /**
     * @test
     */
    public function it_returns_a_new_model_for_first_where_or_create(): void
    {
        /** @var ModelWithRepository $result */
        $result = $this->repository->firstWhereOrCreate(
            ['foo' => 'something'],
            ['bar' => 'stuff'],
        );

        static::assertTrue($result->exists);
        static::assertSame('something', $result->foo);
        static::assertSame('stuff', $result->bar);

        $this->assertDatabaseCount('model_with_repositories', 4);
    }

    /**
     * @param Collection<int, ModelWithRepository> $expected
     * @param Collection<int, ModelWithRepository> $actual
     */
    protected function assertSameModels(Collection $expected, Collection $actual): void
    {
        static::assertSameSize($expected, $actual);

        foreach ($expected as $index => $expectedModel) {
            /** @var Model $actualModel */
            $actualModel = $actual->get($index);

            static::assertTrue($actualModel->is($expectedModel));
        }
    }

    /**
     * @param Collection<int, ModelWithRepository> $models
     * @param string                          $column
     */
    protected function assertModelsHaveColumn(Collection $models, string $column): void
    {
        static::assertEmpty(
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
        static::assertEmpty(
            $models->pluck($column)->filter(function ($value): bool {
                return $value !== null;
            })->values()->all(),
            "The models should not have column {$column}",
        );
    }
}
