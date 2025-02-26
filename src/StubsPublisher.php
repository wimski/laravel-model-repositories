<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories;

use Illuminate\Filesystem\Filesystem;
use Wimski\ModelRepositories\Contracts\Resolvers\StubsPathResolverInterface;
use Wimski\ModelRepositories\Contracts\StubsPublisherInterface;

class StubsPublisher implements StubsPublisherInterface
{
    protected const array STUB_FILES = [
        'model.repository.interface.stub',
        'model.repository.stub',
    ];

    protected Filesystem $filesystem;
    protected StubsPathResolverInterface $stubsPathResolver;

    public function __construct(Filesystem $filesystem, StubsPathResolverInterface $stubsPathResolver)
    {
        $this->filesystem        = $filesystem;
        $this->stubsPathResolver = $stubsPathResolver;
    }

    public function publish(bool $overwriteFiles = false): void
    {
        $this->createStubsDirectoryIfNotExists();
        $this->copyStubFiles($overwriteFiles);
    }

    protected function createStubsDirectoryIfNotExists(): void
    {
        $stubsDirectory = $this->stubsPathResolver->resolveAppPath('');

        if ($this->filesystem->isDirectory($stubsDirectory)) {
            return;
        }

        $this->filesystem->makeDirectory($stubsDirectory, 0755, true);
    }

    protected function copyStubFiles(bool $overwriteFiles): void
    {
        foreach (self::STUB_FILES as $stubFile) {
            $this->copyStubFile($stubFile, $overwriteFiles);
        }
    }

    protected function copyStubFile(string $stubFile, bool $overwriteFiles): void
    {
        $from = $this->stubsPathResolver->resolvePackagePath($stubFile);
        $to   = $this->stubsPathResolver->resolveAppPath($stubFile);

        if ($this->filesystem->exists($to) && ! $overwriteFiles) {
            return;
        }

        $this->filesystem->put($to, $this->filesystem->get($from));
    }
}
