<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeFormDataProvider extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:form-data-provider';

    protected $description = 'Generate a new form data provider';

    protected $type = 'Form data provider';

    public function handle(): ?bool
    {
        $this->directory = 'Form';

        parent::handle();

        return ! $this->registerConfig();
    }

    protected function registerConfig(): bool
    {
        $class = $this->qualifyClass($this->getNameInput());
        $form_data_configuration = Str::of($this->argument('name'))
            ->append('FormDataConfiguration')
            ->snake()
            ->lower()
            ->toString();

        return app(YamlConfigContract::class)
            ->get('services')
            ->register([
                "prestashop.module.{$this->getModuleLowerSnake()}.form.{$this->getNameLowerSnake()}" => [
                    'class' => $class,
                    'arguments' => [
                        "@prestashop.module.{$this->getModuleLowerSnake()}.form.{$form_data_configuration}",
                    ],
                ],
            ]);
    }

    protected function getNameInput(): string
    {
        return Str::of(parent::getNameInput())
            ->ucfirst()
            ->append('FormDataProvider')
            ->toString();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Form';
    }

    protected function getStub(): string
    {
        return base_path('stubs/form-data-provider.stub');
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form data provider.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
