<?php

namespace App\Services\Yaml;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class YamlConfigService
{
    public Collection $data;

    public function __construct(
        protected ?string $file = null
    ) {
        if ($this->file) {
            if (config('app.env') === 'testing') {
                $this->file = getcwd().'/module-test/config/'.$this->file;
            } else {
                $this->file = getcwd().'/config/'.$this->file;
            }
        }

        $this->data = Collection::make();
    }

    public function load(?string $file = null): self
    {
        $file = $file ?? $this->file;

        if (File::exists($file)) {
            $this->data = collect(Yaml::parseFile($file));
        }

        return $this;
    }

    public function write(?string $file = null): bool
    {
        $file = $file ?? $this->file;

        return File::put(
            $file,
            Yaml::dump($this->data->toArray())
        );
    }
}
