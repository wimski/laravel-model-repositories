<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Wimski\ModelRepositories\Tests\Integration\AbstractIntegrationTest;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Repositories\ModelWithRepositoryRepository;

class ModelRepositoryTest extends AbstractIntegrationTest
{
    protected ModelWithRepositoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ModelWithRepositoryRepository(new ModelWithRepository());
    }

    /**
     * @test
     */
    public function it_finds_a_model_by_id(): void
    {
        /** @var ModelWithRepository $model */
        $model = ModelWithRepository::factory()->create([
            'id' => 23,
        ]);

        $result = $this->repository->findOrFail(23);

        static::assertTrue($model->is($result));
    }

    /**
     * @test
     */
    public function it_finds_many_models_by_id(): void
    {
        $models = ModelWithRepository::factory()->createMany([
            ['id' => 23],
            ['id' => 36],
            ['id' => 51],
            ['id' => 62],
        ]);

        ModelWithRepository::factory()->count(3)->create();

        $result = $this->repository->findMany([23, 36, 51, 62]);

        $this->assertSameModels($models, $result);
    }

    /**
     * @test
     */
    public function it_returns_all_models(): void
    {
        $models = ModelWithRepository::factory()->count(5)->create();

        $result = $this->repository->all();

        $this->assertSameModels($models, $result);
    }

    /**
     * @test
     */
    public function it_returns_all_models_with_a_specific_column(): void
    {
        $models = ModelWithRepository::factory()->count(5)->create();

        $result = $this->repository->all('id');

        $this->assertSameModels($models, $result);

        static::assertEmpty(
            $result->pluck('foo')->filter(function (?string $foo): bool {
                return $foo !== null;
            })->values()->all(),
        );

        static::assertEmpty(
            $result->pluck('bar')->filter(function (?string $bar): bool {
                return $bar !== null;
            })->values()->all(),
        );
    }

    /**
     * @test
     */
    public function it_returns_all_models_with_specific_columns(): void
    {
        $models = ModelWithRepository::factory()->count(5)->create();

        $result = $this->repository->all(['id', 'bar']);

        $this->assertSameModels($models, $result);

        static::assertEmpty(
            $result->pluck('foo')->filter(function (?string $foo): bool {
                return $foo !== null;
            })->values()->all(),
        );

        static::assertCount(
            5,
            $result->pluck('bar')->filter(function (?string $bar): bool {
                return $bar !== null;
            })->values()->all(),
        );
    }

    /**
     * @param Collection<Model> $expected
     * @param Collection<Model> $actual
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
}
