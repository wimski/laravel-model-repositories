<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Resolvers;

use Exception;
use Illuminate\Contracts\Config\Repository as Config;
use Wimski\ModelRepositories\Contracts\Resolvers\NamespaceResolverInterface;
use Wimski\ModelRepositories\DataObjects\NamespaceDataObject;

readonly class NamespaceResolver implements NamespaceResolverInterface
{
    public function __construct(
        protected Config $config,
    ) {
    }

    public function resolve(string $model): NamespaceDataObject
    {
        $model = ltrim($model, '\\');

        /** @var array<string, string>[] $namespaces */
        $namespaces = $this->config->get('model-repositories.namespaces');

        foreach ($namespaces as $namespace) {
            $modelNamespace = ltrim(trim($namespace['models']), '\\');

            if (str_starts_with($model, $modelNamespace)) {
                return new NamespaceDataObject($namespace);
            }
        }

        throw new Exception("No namespace found for {$model}");
    }
}
