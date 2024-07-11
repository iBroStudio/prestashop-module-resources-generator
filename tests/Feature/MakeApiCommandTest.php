<?php

use App\Commands\MakeApi;
use App\Enums\ApiAuthManagers;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can generate api with auth', function () {
    artisan(MakeApi::class, ['name' => 'test', '--force' => true])
        ->expectsQuestion('Authentication type?', ApiAuthManagers::BEARER->label())
        ->assertExitCode(Command::SUCCESS);

    expect(getcwd().'/Api/TestApi/TestApi.php')->toBeFile()
        ->and(
            file_get_contents(getcwd().'/Api/TestApi/TestApi.php')
        )->toContain('final class TestApi extends ApiClient')
        ->toContain('const AUTH = '.ApiAuthManagers::BEARER->value.'::class;');
});

it('can generate api without auth', function () {
    artisan(MakeApi::class, ['name' => 'test', '--force' => true])
        ->expectsQuestion('Authentication type?', ApiAuthManagers::NONE->label())
        ->assertExitCode(Command::SUCCESS);

    expect(getcwd().'/Api/TestApi/TestApi.php')->toBeFile()
        ->and(
            file_get_contents(getcwd().'/Api/TestApi/TestApi.php')
        )->toContain('final class TestApi extends ApiClient')
        ->toContain('const AUTH = '.ApiAuthManagers::NONE->value.';');
});
