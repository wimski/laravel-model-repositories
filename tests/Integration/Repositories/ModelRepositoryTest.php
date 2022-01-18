<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration\Repositories;

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
     * @var Collection<ModelWithRepository>
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
    public function it_returns_a_model_with_a_specific_columns_for_find(): void
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
    public function it_returns_a_model_with_a_specific_columns_for_find_or_fail(): void
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
    public function it_returns_a_collection_with_a_specific_columns_for_find_many(): void
    {
        $result = $this->repository->findMany([23, 36, 51], 'id', 'foo');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
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
    public function it_returns_a_collection_with_a_specific_columns_for_all(): void
    {
        $result = $this->repository->all('id', 'foo');

        $this->assertModelsHaveColumn($result, 'id');
        $this->assertModelsHaveColumn($result, 'foo');
        $this->assertModelsDoNotHaveColumn($result, 'bar');
    }

    /**
     * @param Collection<ModelWithRepository> $expected
     * @param Collection<ModelWithRepository> $actual
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
     * @param Collection<ModelWithRepository> $models
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
     * @param Collection<ModelWithRepository> $models
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
