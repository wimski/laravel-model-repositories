<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Unit\Resolvers;

use Exception;
use Illuminate\Contracts\Config\Repository as Config;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Wimski\ModelRepositories\Resolvers\NamespaceResolver;
use Wimski\ModelRepositories\Tests\Unit\AbstractUnitTestCase;

class NamespaceResolverTest extends AbstractUnitTestCase
{
    protected NamespaceResolver $resolver;
    protected Config&MockInterface $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = Mockery::mock(Config::class);

        $this->resolver = new NamespaceResolver($this->config);
    }

    #[Test]
    public function it_resolves_a_namespace_configuration_for_a_model_class(): void
    {
        $this->config
            ->expects('get')
            ->with('model-repositories.namespaces')
            ->andReturn([
                [
                    'models'       => '\\App\\Models',
                    'contracts'    => '\\App\\Contracts',
                    'repositories' => '\\App\\Repositories',
                ],
            ]);

        $namespaces = $this->resolver->resolve('App\\Models\\Foo\\Bar');

        self::assertSame('\\App\\Models', $namespaces->getModelsNamespace());
    }

    #[Test]
    public function it_throws_an_error_if_no_namespace_configuration_could_be_found_for_a_model_class(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No namespace found for Foo\\Bar');

        $this->config
            ->expects('get')
            ->with('model-repositories.namespaces')
            ->andReturn([]);

        $this->resolver->resolve('Foo\\Bar');
    }
}
