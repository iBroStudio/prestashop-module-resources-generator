<?php

namespace App\Commands;

use App\Commands\Traits\NamespaceAndPath;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeConfigForm extends Command
{
    use NamespaceAndPath;

    protected $name = 'make:config-form';

    protected $description = 'Generate a new config form';

    protected $type = 'Config form';

    public function handle(): int
    {
        $name = Str::lower($this->argument('name'));
        $force = $this->option('force');

        if (
            $this->call(MakeFormType::class, [
                'name' => $name,
                '--force' => $force,
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormDataConfiguration::class, [
                'name' => $name,
                '--force' => $force,
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormDataProvider::class, [
                'name' => $name,
                '--force' => $force,
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormAdminView::class, [
                'name' => $name,
                '--force' => $force,
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        if (
            $this->call(MakeFormAdminController::class, [
                'name' => $name,
                '--force' => $force,
            ]) === Command::FAILURE
        ) {
            return Command::FAILURE;
        }

        return $this->registerConfig() ? Command::SUCCESS : Command::FAILURE;
    }

    protected function registerConfig(): bool
    {
        $name = Str::of($this->argument('name'))
            ->lower()
            ->ucfirst()
            ->append('Configuration')
            ->toString();

        $form_data_provider = Str::of($this->argument('name'))
            ->append('FormDataProvider')
            ->snake()
            ->lower()
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
                        'PrestaShop\Module\DemoSymfonyFormSimple\Form\DemoConfigurationFormType',
                        $name,
                    ],
                ],
            ]);
    }

    protected function getNameInput(): string
    {
        return Str::of($this->argument('name'))
            ->ucfirst()
            ->append('FormDataHandler')
            ->toString();
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
