<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration\Console\Commands;

use Illuminate\Testing\PendingCommand;
use PHPUnit\Framework\Attributes\Test;
use Wimski\ModelRepositories\Tests\Integration\AbstractIntegrationTestCase;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithoutRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithRepository;

class ModelRepositoryMakeCommandTest extends AbstractIntegrationTestCase
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

    #[Test]
    public function it_makes_a_model_repository(): void
    {
        /** @var PendingCommand $command */
        $command = $this->artisan('make:repository', [
            'model' => ModelWithoutRepository::class,
        ]);

        $command
            ->expectsOutput('Repository created successfully.')
            ->execute();

        self::assertFileExists($this->getRepositoryInterfacePath());
        self::assertFileExists($this->getRepositoryPath());

        self::assertSame(
            '<?php

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

        self::assertSame(
            '<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Repositories;

use Wimski\ModelRepositories\Repositories\AbstractModelRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Contracts\Repositories\ModelWithoutRepositoryRepositoryInterface;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithoutRepository;

/**
 * @extends AbstractModelRepository<ModelWithoutRepository>
 */
readonly class ModelWithoutRepositoryRepository extends AbstractModelRepository implements ModelWithoutRepositoryRepositoryInterface
{
    public function __construct(ModelWithoutRepository $model)
    {
        parent::__construct($model);
    }
}
', file_get_contents($this->getRepositoryPath()));
    }

    #[Test]
    public function it_makes_a_model_repository_with_specific_fqns(): void
    {
        $contractDir    = $this->getAppStubPath('Foo');
        $contractFile   = $contractDir . DIRECTORY_SEPARATOR . 'Bar.php';
        $repositoryDir  = $this->getAppStubPath('Bar');
        $repositoryFile = $repositoryDir . DIRECTORY_SEPARATOR . 'Foo.php';

        /** @var PendingCommand $command */
        $command = $this->artisan('make:repository', [
            'model'        => ModelWithoutRepository::class,
            '--contract'   => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Foo\\Bar',
            '--repository' => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Bar\\Foo',
        ]);

        $command
            ->expectsOutput('Repository created successfully.')
            ->execute();

        self::assertFileExists($contractFile);
        self::assertFileExists($repositoryFile);

        self::assertSame(
            '<?php

namespace Wimski\ModelRepositories\Tests\Laravel\App\Foo;

use Wimski\ModelRepositories\Contracts\Repositories\ModelRepositoryInterface;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithoutRepository;

/**
 * @extends ModelRepositoryInterface<ModelWithoutRepository>
 */
interface Bar extends ModelRepositoryInterface
{
}
', file_get_contents($contractFile));

        self::assertSame(
            '<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel\App\Bar;

use Wimski\ModelRepositories\Repositories\AbstractModelRepository;
use Wimski\ModelRepositories\Tests\Laravel\App\Foo\Bar;
use Wimski\ModelRepositories\Tests\Laravel\App\Models\ModelWithoutRepository;

/**
 * @extends AbstractModelRepository<ModelWithoutRepository>
 */
readonly class Foo extends AbstractModelRepository implements Bar
{
    public function __construct(ModelWithoutRepository $model)
    {
        parent::__construct($model);
    }
}
', file_get_contents($repositoryFile));

        unlink($contractFile);
        unlink($repositoryFile);
        rmdir($contractDir);
        rmdir($repositoryDir);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

        self::assertFileExists($this->getRepositoryInterfacePath());
        self::assertFileExists($this->getRepositoryPath());

        self::assertSame('Foo', file_get_contents($this->getRepositoryInterfacePath()));
        self::assertSame('Bar', file_get_contents($this->getRepositoryPath()));

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
