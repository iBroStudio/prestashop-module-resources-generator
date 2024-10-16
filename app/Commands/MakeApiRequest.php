<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Enums\ApiRequestMethods;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApiRequest extends GeneratorCommand
{
    use NamespaceAndPath;

    protected $name = 'make:api-request';

    protected $description = 'Generate a api request';

    protected $type = 'Api request';

    protected string $method;

    public function handle(): ?bool
    {
        $this->method = $this->choice(
            question: 'Request method?',
            choices: ApiRequestMethods::getSelector()
        );

        $this->directory = Str::of(
            Str::replaceFirst(
                $this->rootNamespace(),
                '',
                $this->getDefaultNamespace(
                    $this->rootNamespace()
                )
            )
        )
            ->chopStart('\\')
            ->replace('\\', '/')
            ->toString();

        return parent::handle();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Api\\'.$this->argument('api').'\\Requests';
    }

    protected function getNameInput(): string
    {
        return Str::of(parent::getNameInput())
            ->lower()
            ->whenStartsWith($this->method, function (Stringable $string) {
                return $string->after($this->method);
            })
            ->whenEndsWith('request', function (Stringable $string) {
                return $string->before('request');
            })
            ->title()
            ->prepend(Str::ucfirst($this->method))
            ->append('Request')
            ->toString();
    }

    protected function getStub(): string
    {
        if ($this->method === ApiRequestMethods::GET->value
            && $this->confirm('Does this request need arguments?')
        ) {
            return base_path("stubs/api-{$this->method}-request-with-args.stub");
        }

        return base_path("stubs/api-{$this->method}-request.stub");
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the request'],
            ['api', InputArgument::REQUIRED, 'The name of the api'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
