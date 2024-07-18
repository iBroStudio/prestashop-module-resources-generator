<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Test command';

    public function handle(): int
    {
        return Command::SUCCESS;
    }
}
