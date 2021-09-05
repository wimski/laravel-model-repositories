<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Providers;

use Illuminate\Support\ServiceProvider;
use Wimski\ModelRepositories\Console\Commands\ModelRepositoryMakeCommand;
use Wimski\ModelRepositories\Console\Commands\StubsPublishCommand;
use Wimski\ModelRepositories\Contracts\Resolvers\NamespaceResolverInterface;
use Wimski\ModelRepositories\Contracts\Resolvers\StubsPathResolverInterface;
use Wimski\ModelRepositories\Contracts\StubsPublisherInterface;
use Wimski\ModelRepositories\Resolvers\NamespaceResolver;
use Wimski\ModelRepositories\Resolvers\StubsPathResolver;
use Wimski\ModelRepositories\StubsPublisher;

class ModelRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<string, string>
     */
    protected array $repositories = [];

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'model-repositories');

        $this
            ->registerHelpers()
            ->registerCommands()
            ->registerRepositories();
    }

    public function boot(): void
    {
        $this->publishes([
            $this->getConfigPath() => config_path('model-repositories.php'),
        ]);
    }

    protected function registerHelpers(): self
    {
        $this->app->singleton(NamespaceResolverInterface::class, NamespaceResolver::class);
        $this->app->singleton(StubsPathResolverInterface::class, StubsPathResolver::class);
        $this->app->singleton(StubsPublisherInterface::class, StubsPublisher::class);

        return $this;
    }

    protected function registerCommands(): self
    {
        $this->commands([
            ModelRepositoryMakeCommand::class,
            StubsPublishCommand::class,
        ]);

        return $this;
    }

    protected function registerRepositories(): self
    {
        foreach ($this->repositories as $interface => $repository) {
            $this->app->singleton($interface, $repository);
        }

        return $this;
    }

    protected function getConfigPath(): string
    {
        return dirname(__DIR__, 2) . '/config/model-repositories.php';
    }
}
