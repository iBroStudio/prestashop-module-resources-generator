<?php

use App\Commands\MakeApi;
use App\Enums\ApiAuthManagers;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can generate api with auth', function () {
    artisan(MakeApi::class, ['name' => 'test', '--force' => true])
        ->expectsQuestion('Authentication type?', ApiAuthManagers::BEARER->label())
        ->assertExitCode(Command::SUCCESS);

    expect(getcwd().'/src/Api/Test/TestApi.php')->toBeFile()
        ->and(
            file_get_contents(getcwd().'/src/Api/Test/TestApi.php')
        )
        ->toContain('namespace PrestaShop\Module\ModuleName\Api\Test;')
        ->toContain('final class TestApi extends ApiClient')
        ->toContain('const AUTH = '.ApiAuthManagers::BEARER->value.'::class;');
});

it('can generate api without auth', function () {
    artisan(MakeApi::class, ['name' => 'test', '--force' => true])
        ->expectsQuestion('Authentication type?', ApiAuthManagers::NONE->label())
        ->assertExitCode(Command::SUCCESS);

    expect(getcwd().'/src/Api/Test/TestApi.php')->toBeFile()
        ->and(
            file_get_contents(getcwd().'/src/Api/Test/TestApi.php')
        )->toContain('final class TestApi extends ApiClient')
        ->toContain('const AUTH = '.ApiAuthManagers::NONE->value.';');
});
