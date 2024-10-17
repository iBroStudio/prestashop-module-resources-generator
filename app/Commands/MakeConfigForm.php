<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;

class MakeConfigForm extends Command
{
    use NamespaceAndPath;

    protected $name = 'make:config-form';

    protected $description = 'Generate a new config form';

    protected $type = 'Config form';

    protected string $form;

    protected bool $exists = false;

    public function handle(): int
    {
        $responses = form()
            ->intro('PRESTA GENERATOR:new Config Form')

            ->text(
                label: 'What should the config form be named?',
                default: 'Configuration',
                required: true,
                name: 'name'
            )

            ->add(function ($responses) {
                $this->form = Str::lower($responses['name']);

                if (
                    ($services = app(YamlConfigContract::class)
                        ->get('services')
                        ->data()
                        ->get('services')) &&
                    ($this->exists = Arr::exists(
                        array: $services,
                        key: "prestashop.module.{$this->getModuleLowerSnake()}.form.{$this->getNameLowerSnake()}"
                    ))
                ) {

                    return confirm(
                        label: 'This form already exists. Force creation?',
                        default: false,
                    );
                }

                return false;
            },
                name: 'force'
            )

            ->submit();

        if ($this->exists && ! $responses['force']) {
            info('Aborted.');

            return Command::SUCCESS;
        }

        if (
            $this->call(MakeFormType::class, [
                'name' => $this->form,
                '--force' => $responses['force'],
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormDataConfiguration::class, [
                'name' => $this->form,
                '--force' => $responses['force'],
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormDataProvider::class, [
                'name' => $this->form,
                '--force' => $responses['force'],
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormAdminView::class, [
                'name' => $this->form,
                '--force' => $responses['force'],
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormAdminAbstractController::class, [
                'name' => $this->form,
                '--force' => $responses['force'],
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormAdminController::class, [
                'name' => $this->form,
                '--force' => $responses['force'],
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        return $this->registerConfig() ? Command::SUCCESS : Command::FAILURE;
    }

    protected function registerConfig(): bool
    {
        $name = Str::of($this->form)
            ->lower()
            ->ucfirst()
            ->append('Configuration')
            ->toString();

        $form_data_provider = Str::of($this->form)
            ->append('FormDataProvider')
            ->snake()
            ->lower()
            ->toString();

        $form_type = Str::of($this->rootNamespace())
            ->append('Form\\')
            ->append(Str::ucfirst($this->form))
            ->append('FormType')
            ->toString();

        return app(YamlConfigContract::class)
            ->get('services')
            ->register([
                "prestashop.module.{$this->getModuleLowerSnake()}.form.{$this->getNameLowerSnake()}" => [
                    'class' => 'PrestaShop\PrestaShop\Core\Form\Handler',
                    'arguments' => [
                        '@form.factory',
                        '@prestashop.core.hook.dispatcher',
                        "@prestashop.module.{$this->getModuleLowerSnake()}.form.{$form_data_provider}",
                        $form_type,
                        $name,
                    ],
                ],
            ]);
    }

    protected function getNameInput(): string
    {
        return Str::of($this->form)
            ->ucfirst()
            ->append('FormDataHandler')
            ->toString();
    }
}
