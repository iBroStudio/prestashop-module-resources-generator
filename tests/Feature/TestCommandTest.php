<?php

use App\Commands\TestCommand;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can run the test command', function () {
    artisan(TestCommand::class)
        ->assertExitCode(Command::SUCCESS);
});
