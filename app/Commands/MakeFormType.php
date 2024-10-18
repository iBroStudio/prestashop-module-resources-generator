<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeFormType extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:form-type';

    protected $description = 'Generate a new form type';

    protected $type = 'Form type';

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
                "prestashop.module.{$this->getModuleLowerSnake()}.form.type.{$this->getNameLowerSnake()}" => [
                    'class' => $class,
                    'parent' => 'form.type.translatable.aware',
                    'public' => true,
                    'tags' => [
                        [
                            'name' => 'form.type',
                        ],
                    ],
                ],
            ]);
    }

    protected function getNameInput(): string
    {
        return Str::of(parent::getNameInput())
            ->studly()
            ->append('FormType')
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
            ['{{ ModuleName }}'],
            [$this->getModuleTranslationDomain()],
            $stub
        );
    }

    protected function getStub(): string
    {
        return base_path('stubs/form-type.stub');
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form type'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
