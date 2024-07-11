<?php

namespace App\Commands\Traits;

use Illuminate\Support\Str;

trait NamespaceAndPath
{
    protected function rootNamespace(): string
    {
        $composer = json_decode((string) file_get_contents(getcwd().'/composer.json'), true);

        if (! $namespace = array_search('src/', $composer['autoload']['psr-4'])) {
            return 'PrestaShop\\Module\\ModuleName\\';
        }

        return $namespace;
    }

    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return getcwd().'/'.str_replace('\\', '/', $name).'.php';
    }
}
