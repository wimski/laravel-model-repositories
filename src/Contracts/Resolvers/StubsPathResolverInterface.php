<?php

namespace Wimski\ModelRepositories\Contracts\Resolvers;

interface StubsPathResolverInterface
{
    public function resolvePackagePath(string $stubFile): string;

    public function resolveAppPath(string $stubFile): string;
}
