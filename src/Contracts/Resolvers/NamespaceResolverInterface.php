<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Contracts\Resolvers;

use Exception;
use Wimski\ModelRepositories\DataObjects\NamespaceDataObject;

interface NamespaceResolverInterface
{
    /**
     * @param string $model
     * @return NamespaceDataObject
     * @throws Exception
     */
    public function resolve(string $model): NamespaceDataObject;
}
