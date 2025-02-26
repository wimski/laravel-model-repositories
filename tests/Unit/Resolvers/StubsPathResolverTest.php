<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Unit\Resolvers;

use Illuminate\Contracts\Foundation\Application;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Wimski\ModelRepositories\Resolvers\StubsPathResolver;
use Wimski\ModelRepositories\Tests\Unit\AbstractUnitTestCase;

class StubsPathResolverTest extends AbstractUnitTestCase
{
    protected StubsPathResolver $resolver;
    protected Application&MockInterface $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = Mockery::mock(Application::class);

        $this->resolver = new StubsPathResolver($this->app);
    }

    #[Test]
    public function it_resolves_a_package_path(): void
    {
        $path = $this->resolver->resolvePackagePath('stub-file');

        self::assertSame(
            dirname(__DIR__, 3) .
            DIRECTORY_SEPARATOR .
            'stubs' .
            DIRECTORY_SEPARATOR .
            'model-repositories' .
            DIRECTORY_SEPARATOR .
            'stub-file',
            $path,
        );
    }

    #[Test]
    public function it_resolves_an_app_path(): void
    {
        $this->app
            ->expects('basePath')
            ->with(
                'stubs' .
                DIRECTORY_SEPARATOR .
                'stub-file',
            )
            ->andReturn('app-path');

        $path = $this->resolver->resolveAppPath('stub-file');

        self::assertSame('app-path', $path);
    }
}
