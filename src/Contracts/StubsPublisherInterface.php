<?php

namespace Wimski\ModelRepositories\Contracts;

interface StubsPublisherInterface
{
    public function publish(bool $overwriteFiles = false): void;
}
