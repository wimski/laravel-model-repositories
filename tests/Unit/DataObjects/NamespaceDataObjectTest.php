<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Unit\DataObjects;

use Wimski\ModelRepositories\DataObjects\NamespaceDataObject;
use Wimski\ModelRepositories\Tests\Unit\AbstractUnitTest;

class NamespaceDataObjectTest extends AbstractUnitTest
{
    protected NamespaceDataObject $dataObject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataObject = new NamespaceDataObject([
            'models'       => 'models-namespace',
            'contracts'    => 'contracts-namespace',
            'repositories' => 'repositories-namespace',
        ]);
    }

    /**
     * @test
     */
    public function it_returns_the_model_namespace(): void
    {
        self::assertSame('models-namespace', $this->dataObject->getModelsNamespace());
    }

    /**
     * @test
     */
    public function it_returns_the_contract_namespace(): void
    {
        self::assertSame('contracts-namespace', $this->dataObject->getContractsNamespace());
    }

    /**
     * @test
     */
    public function it_returns_the_repository_namespace(): void
    {
        self::assertSame('repositories-namespace', $this->dataObject->getRepositoriesNamespace());
    }
}
