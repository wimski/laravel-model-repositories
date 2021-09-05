<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Mockery\MockInterface;
use Wimski\ModelRepositories\Contracts\Resolvers\StubsPathResolverInterface;
use Wimski\ModelRepositories\StubsPublisher;

class StubsPublisherTest extends AbstractUnitTest
{
    protected StubsPublisher $publisher;

    /**
     * @var Filesystem|MockInterface
     */
    protected $filesystem;

    /**
     * @var StubsPathResolverInterface|MockInterface
     */
    protected $stubsPathResolver;

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

    /**
     * @test
     */
    public function it_publishes_stubs(): void
    {
        $this->filesystem
            ->shouldReceive('isDirectory')
            ->once()
            ->with('stubs-dir')
            ->andReturnTrue()
            ->getMock()
            ->shouldReceive('exists')
            ->once()
            ->with('to-path-1')
            ->andReturnFalse()
            ->getMock()
            ->shouldReceive('exists')
            ->once()
            ->with('to-path-2')
            ->andReturnTrue()
            ->getMock()
            ->shouldReceive('get')
            ->once()
            ->with('from-path-1')
            ->andReturn('data-1')
            ->getMock()
            ->shouldReceive('put')
            ->once()
            ->with('to-path-1', 'data-1');

        $this->publisher->publish();
    }

    /**
     * @test
     */
    public function it_creates_the_stubs_directory_if_not_exists(): void
    {
        $this->filesystem
            ->shouldReceive('isDirectory')
            ->once()
            ->with('stubs-dir')
            ->andReturnFalse()
            ->getMock()
            ->shouldReceive('makeDirectory')
            ->once()
            ->with('stubs-dir', 0755, true)
            ->getMock()
            ->shouldReceive('exists')
            ->once()
            ->with('to-path-1')
            ->andReturnFalse()
            ->getMock()
            ->shouldReceive('exists')
            ->once()
            ->with('to-path-2')
            ->andReturnTrue()
            ->getMock()
            ->shouldReceive('get')
            ->once()
            ->with('from-path-1')
            ->andReturn('data-1')
            ->getMock()
            ->shouldReceive('put')
            ->once()
            ->with('to-path-1', 'data-1');

        $this->publisher->publish();
    }

    /**
     * @test
     */
    public function it_overwrites_files_when_publishing_stubs(): void
    {
        $this->filesystem
            ->shouldReceive('isDirectory')
            ->once()
            ->with('stubs-dir')
            ->andReturnTrue()
            ->getMock()
            ->shouldReceive('exists')
            ->once()
            ->with('to-path-1')
            ->andReturnFalse()
            ->getMock()
            ->shouldReceive('exists')
            ->once()
            ->with('to-path-2')
            ->andReturnTrue()
            ->getMock()
            ->shouldReceive('get')
            ->once()
            ->with('from-path-1')
            ->andReturn('data-1')
            ->getMock()
            ->shouldReceive('put')
            ->once()
            ->with('to-path-1', 'data-1')
            ->getMock()
            ->shouldReceive('get')
            ->once()
            ->with('from-path-2')
            ->andReturn('data-2')
            ->getMock()
            ->shouldReceive('put')
            ->once()
            ->with('to-path-2', 'data-2');

        $this->publisher->publish(true);
    }

    /**
     * @return StubsPathResolverInterface|MockInterface
     */
    protected function mockStubsPathResolver()
    {
        /** @var StubsPathResolverInterface|MockInterface $stubsPathResolver */
        $stubsPathResolver = Mockery::mock(StubsPathResolverInterface::class)
            ->shouldReceive('resolveAppPath')
            ->once()
            ->with('')
            ->andReturn('stubs-dir')
            ->getMock()
            ->shouldReceive('resolvePackagePath')
            ->once()
            ->with('model.repository.interface.stub')
            ->andReturn('from-path-1')
            ->getMock()
            ->shouldReceive('resolveAppPath')
            ->once()
            ->with('model.repository.interface.stub')
            ->andReturn('to-path-1')
            ->getMock()
            ->shouldReceive('resolvePackagePath')
            ->once()
            ->with('model.repository.stub')
            ->andReturn('from-path-2')
            ->getMock()
            ->shouldReceive('resolveAppPath')
            ->once()
            ->with('model.repository.stub')
            ->andReturn('to-path-2')
            ->getMock();

        return $stubsPathResolver;
    }
}
