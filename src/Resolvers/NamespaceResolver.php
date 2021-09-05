<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Resolvers;

use Exception;
use Illuminate\Contracts\Config\Repository as Config;
use Wimski\ModelRepositories\Contracts\Resolvers\NamespaceResolverInterface;
use Wimski\ModelRepositories\DataObjects\NamespaceDataObject;

class NamespaceResolver implements NamespaceResolverInterface
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function resolve(string $model): NamespaceDataObject
    {
        $model = ltrim($model, '\\');

        /** @var array<string, string>[] $namespaces */
        $namespaces = $this->config->get('model-repositories.namespaces');

        foreach ($namespaces as $namespace) {
            $modelNamespace = ltrim(trim($namespace['models']), '\\');

            if (strpos($model, $modelNamespace) === 0) {
                return new NamespaceDataObject($namespace);
            }
        }

        throw new Exception('No namespace found for ' . $model);
    }
}
