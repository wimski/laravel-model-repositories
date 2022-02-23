<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;

/**
 * @extends Factory<ModelWithRepository>
 */
class ModelWithRepositoryFactory extends Factory
{
    protected $model = ModelWithRepository::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'foo' => $this->faker->word(),
            'bar' => $this->faker->word(),
        ];
    }
}
