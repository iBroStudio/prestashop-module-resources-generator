<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Enums\ApiAuthManagers;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApi extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:api';

    protected $description = 'Generate a new api client';

    protected $type = 'Api client';

    protected string $directory;

    protected function getNameInput(): string
    {
        $this->directory = Str::of(parent::getNameInput())
            ->lower()
            ->whenEndsWith('api', function (Stringable $string) {
                return $string->before('api');
            })
            ->title();

        return Str::of($this->directory)->append('Api');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Api\\'.$this->getNameInput();
    }

    protected function replaceClass($stub, $name): array|string
    {
        $stub = parent::replaceClass($stub, $name);

        $authenticators = ApiAuthManagers::getSelector();

        $authenticator = $this->choice(
            question: 'Authentication type?',
            choices: array_keys($authenticators)
        );

        return str_replace(
            ['DummyAuthenticatorClass', '{{ use_authenticator }}'],
            [
                $authenticators[$authenticator] !== 'null'
                    ? $authenticators[$authenticator].'::class'
                    : 'null',
                $authenticators[$authenticator] !== 'null'
                    ? "\nuse IBroStudio\\ModuleHelper\Api\\AuthManagers\\{$authenticators[$authenticator]};"
                    : '',
            ],
            $stub
        );
    }

    protected function getStub(): string
    {
        return getcwd().'/stubs/api.stub';
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the api'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the console command already exists'],
        ];
    }
}
