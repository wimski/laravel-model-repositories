<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Console\Commands;

use Illuminate\Console\Command;
use Wimski\ModelRepositories\Contracts\StubsPublisherInterface;

class StubsPublishCommand extends Command
{
    protected $signature = 'repository:stubs {--force : Overwrite any existing files}';
    protected $description = 'Publish all stubs that are available for customization';

    public function handle(StubsPublisherInterface $stubsPublisher): void
    {
        /** @var bool $force */
        $force = $this->option('force');

        $stubsPublisher->publish($force);

        $this->info('Repository stubs published successfully.');
    }
}
