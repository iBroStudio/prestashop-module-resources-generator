<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeFormAdminController extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:form-admin-controller';

    protected $description = 'Generate a new form admin controller';

    protected $type = 'Form admin controller';

    protected string $form_name;

    public function handle(): ?bool
    {
        $this->directory = 'Controller';

        $this->form_name = Str::of($this->argument('name'))
            ->snake()
            ->lower()
            ->append('_form');

        parent::handle();

        return ! $this->registerRoute();
    }

    protected function registerRoute(): bool
    {
        $class = $this->qualifyClass($this->getNameInput());
        $route = Str::chopEnd($this->getNameLowerSnake(), '_controller');
        $legacy = "Admin{$this->getModuleTranslationDomain()}{$this->getNameInput()}";

        return app(YamlConfigContract::class)
            ->get('routes')
            ->register([
                "{$this->getModuleLowerSnake()}_{$route}" => [
                    'path' => "/{$this->getModuleLowerSnake()}/{$route}",
                    'methods' => ['GET', 'POST'],
                    'defaults' => [
                        '_controller' => "{$class}::index",
                        '_legacy_controller' => $legacy,
                        '_legacy_link' => $legacy,
                    ],
                ],
            ]);
    }

    protected function getNameInput(): string
    {
        return Str::of(parent::getNameInput())
            ->ucfirst()
            ->append('Controller')
            ->toString();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Controller';
    }

    protected function replaceClass($stub, $name): array|string
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace(
            ['{{ module_directory }}', '{{ module_controller_route }}', '{{ form_name }}'],
            [
                $this->getModuleDirectory(),
                $this->form_name.'_route',
                $this->form_name,
            ],
            $stub
        );
    }

    protected function getStub(): string
    {
        return base_path('stubs/form-admin-controller.stub');
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form admin controller.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
