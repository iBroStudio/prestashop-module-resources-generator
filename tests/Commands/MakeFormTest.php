<?php

use App\Commands\MakeConfigForm;
use App\Commands\MakeFormAdminController;
use App\Commands\MakeFormAdminView;
use App\Commands\MakeFormDataConfiguration;
use App\Commands\MakeFormDataProvider;
use App\Commands\MakeFormType;
use App\Services\Yaml\YamlConfigContract;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can generate form type', function () {
    $file = getcwd().'/src/Form/TestFormType.php';
    File::delete($file);
    File::delete(getcwd().'/src/config/services.yml');

    artisan(MakeFormType::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('services');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('class TestFormType extends TranslatorAwareType')
        ->toContain("'Field label', 'Modules.ModuleName.Admin'")
        ->and(
            $yaml->data()->get('services')['prestashop.module.module_name.form.type.test_form_type']
        )->toMatchArray([
            'class' => 'PrestaShop\Module\ModuleName\Form\TestFormType',
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
    $file = getcwd().'/src/Form/TestFormDataConfiguration.php';
    File::delete($file);
    File::delete(getcwd().'/src/config/services.yml');

    artisan(MakeFormDataConfiguration::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('services');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('final class TestFormDataConfiguration implements DataConfigurationInterface')
        ->toContain('MODULE_NAME_KEY1')
        ->and(
            $yaml->data()->get('services')['prestashop.module.module_name.form.test_form_data_configuration']
        )->toMatchArray([
            'class' => 'PrestaShop\Module\ModuleName\Form\TestFormDataConfiguration',
            'arguments' => [
                '@prestashop.adapter.legacy.configuration',
            ],
        ]);
});

it('can generate form data provider', function () {
    $file = getcwd().'/src/Form/TestFormDataProvider.php';
    File::delete($file);
    File::delete(getcwd().'/src/config/services.yml');

    artisan(MakeFormDataProvider::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('services');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('final class TestFormDataProvider implements FormDataProviderInterface')
        ->and(
            $yaml->data()->get('services')['prestashop.module.module_name.form.test_form_data_provider']
        )->toMatchArray([
            'class' => 'PrestaShop\Module\ModuleName\Form\TestFormDataProvider',
            'arguments' => [
                '@prestashop.module.module_name.form.test_form_data_configuration',
            ],
        ]);
});

it('can generate form admin controller', function () {
    $file = getcwd().'/src/Controller/TestController.php';
    File::delete($file);
    File::delete(getcwd().'/src/config/routes.yml');

    artisan(MakeFormAdminController::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $yaml = app(YamlConfigContract::class)->get('routes');

    expect($file)->toBeFile()
        ->and(File::get($file))
        ->toContain('class TestController extends FrameworkBundleAdminController')
        ->toContain('prestashop.module.prestashop-module-resources-generator.form.test_form_data_handler')
        ->toContain('$this->redirectToRoute(\'test_form_route\')')
        ->toContain('return $this->render(\'@Modules/prestashop-module-resources-generator/views/templates/admin/form.html.twig\'')
        ->toContain('\'test_form\' => $form->createView()')
        ->and(
            $yaml->data()->get('module_name_test')
        )->toMatchArray([
            'path' => '/module_name/test',
            'methods' => ['GET', 'POST'],
            'defaults' => [
                '_controller' => 'PrestaShop\Module\ModuleName\Controller\TestController::index',
                '_legacy_controller' => 'AdminModuleNameTestController',
                '_legacy_link' => 'AdminModuleNameTestController',
            ],
        ]);
});

it('can generate form admin view', function () {
    $file = getcwd().'/src/views/templates/admin/testForm.html.twig';
    File::delete($file);

    artisan(MakeFormAdminView::class, ['name' => 'test', '--force' => true])
        ->assertExitCode(Command::SUCCESS);

    expect($file)->toBeFile()
        ->and(
            File::get($file)
        )->toContain('{{ form_start(testForm) }}')
        ->toContain('Modules.ModuleName.Admin')
        ->toContain('{{ form_widget(testForm) }}')
        ->toContain('{{ form_end(testForm) }}');
});

it('can generate a config form ', function () {
    $formType = getcwd().'/src/Form/TestFormType.php';
    $formDataConfiguration = getcwd().'/src/Form/TestFormDataConfiguration.php';
    $formDataProvider = getcwd().'/src/Form/TestFormDataProvider.php';
    $formController = getcwd().'/src/Controller/TestController.php';
    $formView = getcwd().'/src/views/templates/admin/testForm.html.twig';
    $serviceYaml = getcwd().'/src/config/services.yml';
    $routeYaml = getcwd().'/src/config/routes.yml';

    File::delete($formType);
    File::delete($formDataConfiguration);
    File::delete($formDataProvider);
    File::delete($formController);
    File::delete($formView);
    File::delete($serviceYaml);
    File::delete($routeYaml);

    artisan(MakeConfigForm::class, ['name' => 'test', '--force' => true])
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
            $yaml->data()->get('services')['prestashop.module.module_name.form.test_form_data_handler']
        )->toMatchArray([
            'class' => 'PrestaShop\PrestaShop\Core\Form\Handler',
            'arguments' => [
                '@form.factory',
                '@prestashop.core.hook.dispatcher',
                '@prestashop.module.module_name.form.test_form_data_provider',
                'PrestaShop\Module\DemoSymfonyFormSimple\Form\DemoConfigurationFormType',
                'TestConfiguration',
            ],
        ]);
});
