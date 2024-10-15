<?php

use Symfony\Component\Process\Process;

it('can run the build', function () {

    $build = new Process([PHP_BINARY, __DIR__.'/../../builds/presta-generator', 'test']);
    $build->run();

    expect($build->isSuccessful())->toBeTrue();
});
