<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Laravel;

use Illuminate\Foundation\Application as Laravel;

class Application extends Laravel
{
    public function getNamespace(): string
    {
        return 'Wimski\\ModelRepositories\\Tests\\Laravel\\App';
    }

    public function path($path = ''): string
    {
        $appPath = $this->appPath ?: $this->basePath . DIRECTORY_SEPARATOR . 'App';

        return $appPath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
