<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeFormDataConfiguration extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:form-data-config';

    protected $description = 'Generate a new form data configuration';

    protected $type = 'Form data configuration';

    public function handle(): ?bool
    {
        $this->directory = 'Form';

        parent::handle();

        return ! $this->registerConfig();
    }

    protected function registerConfig(): bool
    {
        $class = $this->qualifyClass($this->getNameInput());

        return app(YamlConfigContract::class)
            ->get('services')
            ->register([
                "prestashop.module.{$this->getModuleLowerSnake()}.form.{$this->getNameLowerSnake()}" => [
                    'class' => $class,
                    'arguments' => [
                        '@prestashop.adapter.legacy.configuration',
                    ],
                ],
            ]);
    }

    protected function getNameInput(): string
    {
        return Str::of(parent::getNameInput())
            ->studly()
            ->append('FormDataConfiguration')
            ->toString();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Form';
    }

    protected function replaceClass($stub, $name): array|string
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace(
            ['{{ MODULE_CONFIG }}'],
            [$this->getModuleUpperSnake()],
            $stub
        );
    }

    protected function getStub(): string
    {
        return base_path('stubs/form-data-config.stub');
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form data configuration.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
