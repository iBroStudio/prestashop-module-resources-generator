<?php

namespace App\Services\Yaml;

use Illuminate\Support\Collection;

interface YamlConfigContract
{
    public function data(): Collection;

    public function register(array $values): bool;
}
