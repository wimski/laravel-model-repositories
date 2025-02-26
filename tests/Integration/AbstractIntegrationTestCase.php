<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Tests\Integration;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use RuntimeException;
use Wimski\ModelRepositories\Tests\Laravel\App\Providers\ModelRepositoryServiceProvider;
use Wimski\ModelRepositories\Tests\Laravel\Application;

abstract class AbstractIntegrationTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getApplication()->make(Config::class)->set('model-repositories.namespaces', [
            [
                'models'       => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Models',
                'contracts'    => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Contracts\\Repositories',
                'repositories' => 'Wimski\\ModelRepositories\\Tests\\Laravel\\App\\Repositories',
            ],
        ]);
    }

    protected function getApplication(): BaseApplication
    {
        if (! $this->app) {
            throw new RuntimeException('Application should be setup');
        }

        return $this->app;
    }

    protected function getPackageProviders($app): array
    {
        return [
            ModelRepositoryServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(self::getLaravelPath('Database' . DIRECTORY_SEPARATOR . 'migrations'));
    }

    protected function resolveApplication(): BaseApplication
    {
        return (new ApplicationBuilder(new Application($this->getApplicationBasePath())))
            ->withProviders()
            ->withMiddleware(static function ($middleware): void {
                //
            })
            ->withCommands()
            ->create();
    }

    public static function applicationBasePath(): string
    {
        return self::getLaravelPath('');
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
        return self::getLaravelPath(
            'App' .
            DIRECTORY_SEPARATOR .
            $file,
        );
    }

    protected function getStubsPath(string $file): string
    {
        return self::getLaravelPath(
            'stubs' .
            DIRECTORY_SEPARATOR .
            $file,
        );
    }
}
