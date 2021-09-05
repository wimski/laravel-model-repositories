<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration\Console\Commands;

use Illuminate\Testing\PendingCommand;
use Wimski\ModelRepositories\Tests\Integration\AbstractIntegrationTest;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithoutRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;

class ModelRepositoryMakeCommandTest extends AbstractIntegrationTest
{
    protected function tearDown(): void
    {
        parent::tearDown();

        if (file_exists($this->getRepositoryInterfacePath())) {
            unlink($this->getRepositoryInterfacePath());
        }

        if (file_exists($this->getRepositoryPath())) {
            unlink($this->getRepositoryPath());
        }
    }

    /**
     * @test
     */
    public function it_makes_a_model_repository(): void
    {
        /** @var PendingCommand $command */
        $command = $this->artisan('make:repository', [
            'model' => ModelWithoutRepository::class,
        ]);

        $command
            ->expectsOutput('Repository created successfully.')
            ->execute();

        static::assertFileExists($this->getRepositoryInterfacePath());
        static::assertFileExists($this->getRepositoryPath());

        static::assertSame(
            '<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Contracts\Repositories;

use Wimski\ModelRepositories\Contracts\Repositories\ModelRepositoryInterface;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithoutRepository;

/**
 * @extends ModelRepositoryInterface<ModelWithoutRepository>
 */
interface ModelWithoutRepositoryRepositoryInterface extends ModelRepositoryInterface
{
}
', file_get_contents($this->getRepositoryInterfacePath()));

        static::assertSame(
            '<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Repositories;

use Wimski\ModelRepositories\Repositories\AbstractModelRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Contracts\Repositories\ModelWithoutRepositoryRepositoryInterface;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithoutRepository;

/**
 * @extends AbstractModelRepository<ModelWithoutRepository>
 */
class ModelWithoutRepositoryRepository extends AbstractModelRepository implements ModelWithoutRepositoryRepositoryInterface
{
    public function __construct(ModelWithoutRepository $model)
    {
        $this->model = $model;
    }
}
', file_get_contents($this->getRepositoryPath()));
    }

    /**
     * @test
     */
    public function it_returns_an_error_if_no_namespace_configuration_could_be_found_for_the_model_class(): void
    {
        /** @var PendingCommand $command */
        $command = $this->artisan('make:repository', [
            'model' => 'Foo\\Bar',
        ]);

        $command
            ->expectsOutput('No namespace found for Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Foo\\Bar')
            ->execute();
    }

    /**
     * @test
     */
    public function it_returns_an_error_if_either_of_the_repository_files_already_exists(): void
    {
        /** @var PendingCommand $command */
        $command = $this->artisan('make:repository', [
            'model' => ModelWithRepository::class,
        ]);

        $command
            ->expectsOutput('Repository already exists!')
            ->execute();
    }

    /**
     * @test
     */
    public function it_uses_the_published_stubs_when_available(): void
    {
        $interfaceStub  = $this->getStubsPath('model.repository.interface.stub');
        $repositoryStub = $this->getStubsPath('model.repository.stub');

        file_put_contents($interfaceStub, 'Foo');
        file_put_contents($repositoryStub, 'Bar');

        /** @var PendingCommand $command */
        $command = $this->artisan('make:repository', [
            'model' => ModelWithoutRepository::class,
        ]);

        $command
            ->expectsOutput('Repository created successfully.')
            ->execute();

        static::assertFileExists($this->getRepositoryInterfacePath());
        static::assertFileExists($this->getRepositoryPath());

        static::assertSame('Foo', file_get_contents($this->getRepositoryInterfacePath()));
        static::assertSame('Bar', file_get_contents($this->getRepositoryPath()));

        unlink($interfaceStub);
        unlink($repositoryStub);
    }

    protected function getRepositoryInterfacePath(): string
    {
        return $this->getAppStubPath(
            'Contracts' .
            DIRECTORY_SEPARATOR .
            'Repositories' .
            DIRECTORY_SEPARATOR .
            'ModelWithoutRepositoryRepositoryInterface.php',
        );
    }

    protected function getRepositoryPath(): string
    {
        return $this->getAppStubPath(
            'Repositories' .
            DIRECTORY_SEPARATOR .
            'ModelWithoutRepositoryRepository.php',
        );
    }
}
