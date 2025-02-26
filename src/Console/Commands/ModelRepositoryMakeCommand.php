<?php

declare(strict_types=1);

namespace Wimski\ModelRepositories\Console\Commands;

use Exception;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Wimski\ModelRepositories\Contracts\Resolvers\NamespaceResolverInterface;
use Wimski\ModelRepositories\Contracts\Resolvers\StubsPathResolverInterface;
use Wimski\ModelRepositories\DataObjects\NamespaceDataObject;

class ModelRepositoryMakeCommand extends GeneratorCommand
{
    protected NamespaceDataObject $namespace;
    protected string $model;
    protected $name = 'make:repository';
    protected $description = 'Create a new model repository class';

    public function __construct(
        Filesystem $files,
        protected readonly NamespaceResolverInterface $namespaceResolver,
        protected readonly StubsPathResolverInterface $stubsPathResolver
    ) {
        parent::__construct($files);
    }

    public function handle(): ?bool
    {
        $this->model = $this->qualifyClass($this->getModelInput());

        if (! $this->getContractInput() || ! $this->getRepositoryInput()) {
            try {
                $this->namespace = $this->namespaceResolver->resolve($this->model);
            } catch (Exception $exception) {
                $this->error($exception->getMessage());
                return false;
            }
        }

        $contract   = $this->getContract();
        $repository = $this->getRepository();

        if ($this->alreadyExists($contract) || $this->alreadyExists($repository)) {
            $this->error('Repository already exists!');
            return false;
        }

        $contractPath   = $this->getPath($contract);
        $repositoryPath = $this->getPath($repository);

        $this->makeDirectory($contractPath);
        $this->makeDirectory($repositoryPath);

        $this->files->put($contractPath, $this->sortImports($this->buildContractClass($contract)));
        $this->files->put($repositoryPath, $this->sortImports($this->buildRepositoryClass($contract, $repository)));

        $this->info('Repository created successfully.');

        return null;
    }

    protected function getModelInput(): string
    {
        /** @var string $argument */
        $argument = $this->argument('model');

        return trim($argument);
    }

    protected function getContractInput(): ?string
    {
        /** @var ?string $option */
        $option = $this->option('contract');

        return $option ? trim($option) : $option;
    }

    protected function getRepositoryInput(): ?string
    {
        /** @var ?string $option */
        $option = $this->option('repository');

        return $option ? trim($option) : $option;
    }

    protected function getContract(): string
    {
        if ($this->getContractInput()) {
            return $this->getContractInput();
        }

        $model = str_replace($this->namespace->getModelsNamespace(), '', $this->model);

        return ltrim($this->namespace->getContractsNamespace() . $model . 'RepositoryInterface', '\\');
    }

    protected function getRepository(): string
    {
        if ($this->getRepositoryInput()) {
            return $this->getRepositoryInput();
        }

        $model = str_replace($this->namespace->getModelsNamespace(), '', $this->model);

        return ltrim($this->namespace->getRepositoriesNamespace() . $model . 'Repository', '\\');
    }

    protected function buildContractClass(string $contract): string
    {
        $stub = $this->files->get(
            $this->resolveStubPath('model.repository.interface.stub'),
        );

        return $this->replaceNamespace($stub, $contract)
            ->replaceModel($stub)
            ->replaceClass($stub, $contract);
    }

    protected function buildRepositoryClass(string $contract, string $repository): string
    {
        $stub = $this->files->get(
            $this->resolveStubPath('model.repository.stub'),
        );

        return $this->replaceNamespace($stub, $repository)
            ->replaceModel($stub)
            ->replaceContract($stub, $contract)
            ->replaceClass($stub, $repository);
    }

    protected function replaceModel(string &$stub): self
    {
        $class = str_replace($this->getNamespace($this->model) . '\\', '', $this->model);

        $stub = str_replace('{{ namespacedModel }}', $this->model, $stub);
        $stub = str_replace('{{ model }}', $class, $stub);

        return $this;
    }

    protected function replaceContract(string &$stub, string $contract): self
    {
        $class = str_replace($this->getNamespace($contract) . '\\', '', $contract);

        $stub = str_replace('{{ namespacedRepositoryInterface }}', $contract, $stub);
        $stub = str_replace('{{ repositoryInterface }}', $class, $stub);

        return $this;
    }

    protected function resolveStubPath(string $stub): string
    {
        return $this->files->exists($customPath = $this->stubsPathResolver->resolveAppPath($stub))
            ? $customPath
            : $this->stubsPathResolver->resolvePackagePath($stub);
    }

    protected function getStub(): string
    {
        // This abstract method must be implemented, but is not used.
        return '';
    }

    /**
     * @return array<array-key, array{0: string, 1: int, 2: string}>
     */
    protected function getArguments(): array
    {
        return [
            ['model', InputArgument::REQUIRED, 'The FQN of the model to make a repository for'],
        ];
    }

    /**
     * @return array<array-key, array{0: string, 1: null, 2: int, 3: string}>
     */
    protected function getOptions(): array
    {
        return [
            ['contract', null, InputOption::VALUE_OPTIONAL, 'The FQN of the repository interface to generate'],
            ['repository', null, InputOption::VALUE_OPTIONAL, 'The FQN of the repository class to generate'],
        ];
    }
}
