<?php

use App\Commands\TestCommand;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function Pest\Laravel\artisan;

it('can run the build', function () {

    $build = new Process([PHP_BINARY, __DIR__ . '/../../builds/presta-generator', 'test']);
    $build->run();

    expect($build->isSuccessful())->toBeTrue();
});
