<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Wimski\ModelRepositories\Contracts\Resolvers\StubsPathResolverInterface;
use Wimski\ModelRepositories\StubsPublisher;

class StubsPublisherTest extends AbstractUnitTestCase
{
    protected StubsPublisher $publisher;
    protected Filesystem&MockInterface $filesystem;
    protected StubsPathResolverInterface&MockInterface $stubsPathResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem        = Mockery::mock(Filesystem::class);
        $this->stubsPathResolver = $this->mockStubsPathResolver();

        $this->publisher = new StubsPublisher(
            $this->filesystem,
            $this->stubsPathResolver,
        );
    }

    #[Test]
    public function it_publishes_stubs(): void
    {
        $this->filesystem->expects('isDirectory')->with('stubs-dir')->andReturnTrue();
        $this->filesystem->expects('exists')->with('to-path-1')->andReturnFalse();
        $this->filesystem->expects('exists')->with('to-path-2')->andReturnTrue();
        $this->filesystem->expects('get')->with('from-path-1')->andReturn('data-1');
        $this->filesystem->expects('put')->with('to-path-1', 'data-1');

        $this->publisher->publish();
    }

    #[Test]
    public function it_creates_the_stubs_directory_if_not_exists(): void
    {
        $this->filesystem->expects('isDirectory')->with('stubs-dir')->andReturnFalse();
        $this->filesystem->expects('makeDirectory')->with('stubs-dir', 0755, true);
        $this->filesystem->expects('exists')->with('to-path-1')->andReturnFalse();
        $this->filesystem->expects('exists')->with('to-path-2')->andReturnTrue();
        $this->filesystem->expects('get')->with('from-path-1')->andReturn('data-1');
        $this->filesystem->expects('put')->with('to-path-1', 'data-1');

        $this->publisher->publish();
    }

    #[Test]
    public function it_overwrites_files_when_publishing_stubs(): void
    {
        $this->filesystem->expects('isDirectory')->with('stubs-dir')->andReturnTrue();
        $this->filesystem->expects('exists')->with('to-path-1')->andReturnFalse();
        $this->filesystem->expects('exists')->with('to-path-2')->andReturnTrue();
        $this->filesystem->expects('get')->with('from-path-1')->andReturn('data-1');
        $this->filesystem->expects('put')->with('to-path-1', 'data-1');
        $this->filesystem->expects('get')->with('from-path-2')->andReturn('data-2');
        $this->filesystem->expects('put')->with('to-path-2', 'data-2');

        $this->publisher->publish(true);
    }

    protected function mockStubsPathResolver(): StubsPathResolverInterface&MockInterface
    {
        $stubsPathResolver = Mockery::mock(StubsPathResolverInterface::class);

        $stubsPathResolver->expects('resolveAppPath')->with('')->andReturn('stubs-dir');
        $stubsPathResolver->expects('resolvePackagePath')->with('model.repository.interface.stub')->andReturn('from-path-1');
        $stubsPathResolver->expects('resolveAppPath')->with('model.repository.interface.stub')->andReturn('to-path-1');
        $stubsPathResolver->expects('resolvePackagePath')->with('model.repository.stub')->andReturn('from-path-2');
        $stubsPathResolver->expects('resolveAppPath')->once()->with('model.repository.stub')->andReturn('to-path-2');

        return $stubsPathResolver;
    }
}
