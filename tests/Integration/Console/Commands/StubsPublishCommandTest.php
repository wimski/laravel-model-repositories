<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration\Console\Commands;

use Illuminate\Testing\PendingCommand;
use Wimski\ModelRepositories\Tests\Integration\AbstractIntegrationTest;

class StubsPublishCommandTest extends AbstractIntegrationTest
{
    protected function tearDown(): void
    {
        parent::tearDown();

        unlink($this->getRepositoryInterfacePath());
        unlink($this->getRepositoryPath());
    }

    /**
     * @test
     */
    public function it_publishes_stubs(): void
    {
        /** @var PendingCommand $command */
        $command = $this->artisan('repository:stubs');

        $command
            ->expectsOutput('Repository stubs published successfully.')
            ->execute();

        self::assertFileExists($this->getRepositoryInterfacePath());
        self::assertFileExists($this->getRepositoryPath());

        self::assertSame('<?php

declare(strict_types=1);

namespace {{ namespace }};

use {{ namespacedModel }};
use Wimski\ModelRepositories\Contracts\Repositories\ModelRepositoryInterface;

/**
 * @extends ModelRepositoryInterface<{{ model }}>
 */
interface {{ class }} extends ModelRepositoryInterface
{
}
', file_get_contents($this->getRepositoryInterfacePath()));

        self::assertSame('<?php

declare(strict_types=1);

namespace {{ namespace }};

use {{ namespacedRepositoryInterface }};
use {{ namespacedModel }};
use Wimski\ModelRepositories\Repositories\AbstractModelRepository;

/**
 * @extends AbstractModelRepository<{{ model }}>
 */
class {{ class }} extends AbstractModelRepository implements {{ repositoryInterface }}
{
    public function __construct({{ model }} $model)
    {
        $this->model = $model;
    }
}
', file_get_contents($this->getRepositoryPath()));
    }

    protected function getRepositoryInterfacePath(): string
    {
        return $this->getStubsPath('model.repository.interface.stub');
    }

    protected function getRepositoryPath(): string
    {
        return $this->getStubsPath('model.repository.stub');
    }
}
