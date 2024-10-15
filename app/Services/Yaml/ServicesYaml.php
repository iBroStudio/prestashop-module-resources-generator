<?php

namespace App\Services\Yaml;

use Illuminate\Support\Collection;

class ServicesYaml implements YamlConfigContract
{
    protected YamlConfigService $yaml;

    public function __construct()
    {
        $this->yaml = app(YamlConfigService::class, ['file' => 'services.yml'])
            ->load();
    }

    public function data(): Collection
    {
        return $this->yaml->data;
    }

    public function register(array $values): bool
    {
        $this->ensure();

        $this->yaml->data->put('services',
            array_merge($this->yaml->data->get('services'), $values)
        );

        return $this->yaml->write();
    }

    protected function ensure(): void
    {
        if (! $this->yaml->data->has('services')) {
            $this->yaml->data->put('services', [
                '_defaults' => [
                    'public' => true,
                ],
            ]);
        }
    }
}
