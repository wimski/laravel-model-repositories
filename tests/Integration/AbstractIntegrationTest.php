<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Foundation\PackageManifest;
use Orchestra\Testbench\TestCase;
use Wimski\ModelRepositories\Tests\Laravel\App\Providers\ModelRepositoryServiceProvider;
use Wimski\ModelRepositories\Tests\Laravel\Application;

abstract class AbstractIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('model-repositories.namespaces', [
            [
                'models'       => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Models',
                'contracts'    => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Contracts\\Repositories',
                'repositories' => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Repositories',
            ],
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            ModelRepositoryServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(static::getLaravelPath('Database' . DIRECTORY_SEPARATOR . 'migrations'));
    }

    protected function resolveApplication()
    {
        return tap(new Application($this->getBasePath()), function ($app) {
            $app->bind(
                'Illuminate\Foundation\Bootstrap\LoadConfiguration',
                'Orchestra\Testbench\Bootstrap\LoadConfiguration'
            );

            PackageManifest::swap($app, $this);
        });
    }

    public static function applicationBasePath(): string
    {
        return $_ENV['APP_BASE_PATH'] ?? static::getLaravelPath('');
    }

    protected static function getLaravelPath(string $path): string
    {
        $path = str_replace([
            '\\',
            '/',
        ], [
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
        ], $path);

        $path = ltrim($path, DIRECTORY_SEPARATOR);

        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Laravel' . DIRECTORY_SEPARATOR . $path;
    }

    protected function getAppStubPath(string $file): string
    {
        return static::getLaravelPath(
            'App' .
            DIRECTORY_SEPARATOR .
            $file,
        );
    }

    protected function getStubsPath(string $file): string
    {
        return static::getLaravelPath(
            'stubs' .
            DIRECTORY_SEPARATOR .
            $file,
        );
    }
}
