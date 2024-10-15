<?php

namespace App\Commands\Traits;

use Illuminate\Support\Str;

trait NamespaceAndPath
{
    protected ?string $directory = null;

    protected function rootNamespace(): string
    {
        $composer = json_decode((string) file_get_contents(getcwd().'/composer.json'), true);

        if (! $namespace = array_search('src/', $composer['autoload']['psr-4'])) {
            return 'PrestaShop\\Module\\ModuleName\\';
        }

        return $namespace;
    }

    protected function getModuleTranslationDomain(): string
    {
        return class_basename($this->rootNamespace());
    }

    protected function getModuleUpperSnake(): string
    {
        return Str::of(class_basename($this->rootNamespace()))
            ->snake()
            ->upper()
            ->toString();
    }

    protected function getModuleLowerSnake(): string
    {
        return Str::of(class_basename($this->rootNamespace()))
            ->snake()
            ->lower()
            ->toString();
    }

    protected function getNameLowerSnake(): string
    {
        return Str::of($this->getNameInput())
            ->snake()
            ->lower()
            ->toString();
    }

    protected function getModuleDirectory(): string
    {
        return Str::afterLast(getcwd(), '/');
    }

    protected function getPath($name): string
    {
        if (! is_null($this->directory)) {
            return getcwd().'/src/'.$this->directory.'/'.$this->getNameInput().'.php';
        } else {
            $name = Str::replaceFirst($this->rootNamespace(), '', $name);

            return getcwd().'/src/'.str_replace('\\', '/', $name).'.php';
        }
    }
}
