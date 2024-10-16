<?php

use App\Commands\MakeConfigForm;
use App\Commands\MakeFormAdminController;
use App\Commands\MakeFormAdminView;
use App\Commands\MakeFormDataConfiguration;
use App\Commands\MakeFormDataProvider;
use App\Commands\MakeFormType;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Support\Facades\File;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can generate form type', function () {
    $file = getcwd().'/module-test/src/Form/TestFormType.php';
    File::delete($file);
    File::delete(getcwd().'/module-test/config/services.yml');

    artisan(MakeFormType::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('services');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('namespace PrestaShop\Module\ModuleTest\Form;')
        ->toContain('class TestFormType extends TranslatorAwareType')
        ->toContain("'Field label', 'Modules.ModuleTest.Admin'")
        ->and(
            $yaml->data()->get('services')['prestashop.module.module_test.form.type.test_form_type']
        )->toMatchArray([
            'class' => 'PrestaShop\Module\ModuleTest\Form\TestFormType',
            'parent' => 'form.type.translatable.aware',
            'public' => true,
            'tags' => [
                [
                    'name' => 'form.type',
                ],
            ],
        ]);
});

it('can generate form data configuration', function () {
    $file = getcwd().'/module-test/src/Form/TestFormDataConfiguration.php';
    File::delete($file);
    File::delete(getcwd().'/module-test/config/services.yml');

    artisan(MakeFormDataConfiguration::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('services');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('namespace PrestaShop\Module\ModuleTest\Form;')
        ->toContain('final class TestFormDataConfiguration implements DataConfigurationInterface')
        ->toContain('MODULE_TEST_KEY1')
        ->and(
            $yaml->data()->get('services')['prestashop.module.module_test.form.test_form_data_configuration']
        )->toMatchArray([
            'class' => 'PrestaShop\Module\ModuleTest\Form\TestFormDataConfiguration',
            'arguments' => [
                '@prestashop.adapter.legacy.configuration',
            ],
        ]);
});

it('can generate form data provider', function () {
    $file = getcwd().'/module-test/src/Form/TestFormDataProvider.php';
    File::delete($file);
    File::delete(getcwd().'/module-test/config/services.yml');

    artisan(MakeFormDataProvider::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('services');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('namespace PrestaShop\Module\ModuleTest\Form;')
        ->toContain('final class TestFormDataProvider implements FormDataProviderInterface')
        ->and(
            $yaml->data()->get('services')['prestashop.module.module_test.form.test_form_data_provider']
        )->toMatchArray([
            'class' => 'PrestaShop\Module\ModuleTest\Form\TestFormDataProvider',
            'arguments' => [
                '@prestashop.module.module_test.form.test_form_data_configuration',
            ],
        ]);
});

it('can generate form admin controller', function () {
    $file = getcwd().'/module-test/src/Controller/TestController.php';
    File::delete($file);
    File::delete(getcwd().'/module-test/config/routes.yml');

    artisan(MakeFormAdminController::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('routes');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('namespace PrestaShop\Module\ModuleTest\Controller;')
        ->toContain('class TestController extends FrameworkBundleAdminController')
        ->toContain('prestashop.module.module_test.form.test_form_data_handler')
        ->toContain('$this->redirectToRoute(\'test_form_route\')')
        ->toContain('return $this->render(\'@Modules/moduletest/views/templates/admin/testForm.html.twig\'')
        ->toContain('\'testForm\' => $form->createView()')
        ->and(
            $yaml->data()->get('module_test_test')
        )->toMatchArray([
            'path' => '/module_test/test',
            'methods' => ['GET', 'POST'],
            'defaults' => [
                '_controller' => 'PrestaShop\Module\ModuleTest\Controller\TestController::index',
                '_legacy_controller' => 'AdminModuleTestTestController',
                '_legacy_link' => 'AdminModuleTestTestController',
            ],
        ]);
});

it('can generate form admin view', function () {
    $file = getcwd().'/module-test/views/templates/admin/testForm.html.twig';
    File::delete($file);

    artisan(MakeFormAdminView::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    expect($file)->toBeFile()
        ->and(
            File::get($file)
        )->toContain('{{ form_start(testForm) }}')
        ->toContain('Modules.ModuleTest.Admin')
        ->toContain('{{ form_widget(testForm) }}')
        ->toContain('{{ form_end(testForm) }}');
});

it('can generate a config form ', function () {
    $formType = getcwd().'/module-test/src/Form/TestFormType.php';
    $formDataConfiguration = getcwd().'/module-test/src/Form/TestFormDataConfiguration.php';
    $formDataProvider = getcwd().'/module-test/src/Form/TestFormDataProvider.php';
    $formController = getcwd().'/module-test/src/Controller/TestController.php';
    $formView = getcwd().'/module-test/views/templates/admin/testForm.html.twig';
    $serviceYaml = getcwd().'/module-test/config/services.yml';
    $routeYaml = getcwd().'/module-test/config/routes.yml';

    File::delete($formType);
    File::delete($formDataConfiguration);
    File::delete($formDataProvider);
    File::delete($formController);
    File::delete($formView);
    File::delete($serviceYaml);
    File::delete($routeYaml);

    Prompt::fake([
        Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE,
        Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE,
        Key::BACKSPACE, //remove default value (Configuration)
        't', 'e', 's', 't', Key::ENTER, //form name
    ]);

    artisan(MakeConfigForm::class)
        ->expectsOutputToContain('What should the config form be named?')
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('services');

    expect($formType)->toBeFile()
        ->and($formDataConfiguration)->toBeFile()
        ->and($formDataProvider)->toBeFile()
        ->and($formController)->toBeFile()
        ->and($formView)->toBeFile()
        ->and($serviceYaml)->toBeFile()
        ->and($routeYaml)->toBeFile()
        ->and(
            $yaml->data()->get('services')['prestashop.module.module_test.form.test_form_data_handler']
        )->toMatchArray([
            'class' => 'PrestaShop\PrestaShop\Core\Form\Handler',
            'arguments' => [
                '@form.factory',
                '@prestashop.core.hook.dispatcher',
                '@prestashop.module.module_test.form.test_form_data_provider',
                'PrestaShop\Module\ModuleTest\Form\TestFormType',
                'TestConfiguration',
            ],
        ]);

    Prompt::fake([
        Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE,
        Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE, Key::BACKSPACE,
        Key::BACKSPACE, //remove default value (Configuration)
        't', 'e', 's', 't', Key::ENTER, //form name
        Key::ENTER, //force creation: false
    ]);

    artisan(MakeConfigForm::class)
        ->expectsOutputToContain('What should the config form be named?')
        ->expectsOutputToContain('This form already exists. Force creation?')
        ->expectsOutputToContain('Aborted.')
        ->assertExitCode(Command::SUCCESS);
});
