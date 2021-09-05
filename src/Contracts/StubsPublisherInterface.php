<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Contracts;

interface StubsPublisherInterface
{
    public function publish(bool $overwriteFiles = false): void;
}
