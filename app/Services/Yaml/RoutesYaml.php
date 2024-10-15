<?php

namespace App\Services\Yaml;

use Illuminate\Support\Collection;

class RoutesYaml implements YamlConfigContract
{
    protected YamlConfigService $yaml;

    public function __construct()
    {
        $this->yaml = app(YamlConfigService::class, ['file' => 'routes.yml'])
            ->load();
    }

    public function data(): Collection
    {
        return $this->yaml->data;
    }

    public function register(array $values): bool
    {

        $this->yaml->data = collect(
            array_merge($this->yaml->data->toArray(), $values)
        );

        return $this->yaml->write();
    }
}
