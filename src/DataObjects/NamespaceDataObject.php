<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\DataObjects;

class NamespaceDataObject
{
    protected string $modelsNamespace;
    protected string $contractsNamespace;
    protected string $repositoriesNamespace;

    /**
     * @param array<string, string> $data
     */
    public function __construct(array $data)
    {
        $this->modelsNamespace       = $data['models'];
        $this->contractsNamespace    = $data['contracts'];
        $this->repositoriesNamespace = $data['repositories'];
    }

    public function getModelsNamespace(): string
    {
        return $this->modelsNamespace;
    }

    public function getContractsNamespace(): string
    {
        return $this->contractsNamespace;
    }

    public function getRepositoriesNamespace(): string
    {
        return $this->repositoriesNamespace;
    }
}
