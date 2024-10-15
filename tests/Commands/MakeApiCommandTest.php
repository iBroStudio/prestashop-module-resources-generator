<?php

use App\Commands\MakeApi;
use App\Enums\ApiAuthManagers;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can generate api with auth', function () {
    $file = getcwd().'/src/Api/Test/TestApi.php';
    File::delete($file);

    artisan(MakeApi::class, ['name' => 'test', '--force' => true])
        ->expectsQuestion('Authentication type?', ApiAuthManagers::BEARER->label())
        ->assertExitCode(Command::SUCCESS);

    expect($file)->toBeFile()
        ->and(
            File::get($file)
        )->toContain('final class TestApi extends ApiClient')
        ->toContain('const AUTH = '.ApiAuthManagers::BEARER->value.'::class;');
});

it('can generate api without auth', function () {
    $file = getcwd().'/src/Api/Test/TestApi.php';
    File::delete($file);

    artisan(MakeApi::class, ['name' => 'test', '--force' => true])
        ->expectsQuestion('Authentication type?', ApiAuthManagers::NONE->label())
        ->assertExitCode(Command::SUCCESS);

    expect($file)->toBeFile()
        ->and(
            File::get($file)
        )->toContain('final class TestApi extends ApiClient')
        ->toContain('const AUTH = '.ApiAuthManagers::NONE->value.';');
});
