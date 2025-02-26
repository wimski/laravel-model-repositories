<?php

namespace Wimski\ModelRepositories\Contracts\Resolvers;

use Exception;
use Wimski\ModelRepositories\DataObjects\NamespaceDataObject;

interface NamespaceResolverInterface
{
    /**
     * @throws Exception
     */
    public function resolve(string $model): NamespaceDataObject;
}
