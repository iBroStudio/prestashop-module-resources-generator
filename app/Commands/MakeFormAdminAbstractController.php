<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeFormAdminAbstractController extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:form-admin-abstract-controller';

    protected $description = 'Generate a new form admin abstract controller';

    protected $type = 'Form admin abstract controller';

    public function handle(): ?bool
    {
        $this->directory = 'Controller/Admin';

        return parent::handle();
    }

    protected function getNameInput(): string
    {
        return Str::of(parent::getNameInput())
            ->ucfirst()
            ->append('AbstractController')
            ->toString();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Controller\\Admin';
    }

    protected function replaceClass($stub, $name): array|string
    {
        $stub = parent::replaceClass($stub, $name);

        $route = Str::of($this->argument('name'))
            ->snake()
            ->lower()
            ->append('_form');

        return str_replace(
            [
                '{{ module_controller_route }}',
                '{{ translation_domain }}',
            ],
            [
                $route.'_route',
                $this->getModuleTranslationDomain(),
            ],
            $stub
        );
    }

    protected function getStub(): string
    {
        return base_path('stubs/form-admin-abstract-controller.stub');
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form admin abstract controller.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
