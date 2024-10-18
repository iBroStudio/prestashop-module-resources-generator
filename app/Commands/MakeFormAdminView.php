<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeFormAdminView extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:form-admin-view';

    protected $description = 'Generate a new form admin view';

    protected $type = 'Form admin view';

    public function handle(): ?bool
    {
        $this->directory = 'admin';

        return parent::handle();
    }

    protected function getNameInput(): string
    {
        return Str::of(parent::getNameInput())
            ->camel()
            ->append('Form')
            ->toString();
    }

    protected function replaceClass($stub, $name): array|string
    {
        return str_replace(
            ['{{ form_name }}', '{{ translation_domain }}'],
            [$this->getNameInput(), $this->getModuleTranslationDomain()],
            $stub
        );
    }

    protected function getStub(): string
    {
        return base_path('stubs/form-admin-view.stub');
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form admin view'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the view even if it already exists'],
        ];
    }

    protected function getPath($name): string
    {
        return Str::of(getcwd())
            ->when(config('app.env') === 'testing', function (Stringable $string) {
                return $string->append('/module-test');
            })
            ->append(
                '/views/templates/'.$this->directory.'/'.$this->getNameInput().'.html.twig'
            );
    }
}
