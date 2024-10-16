<?php

namespace App\Commands\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait NamespaceAndPath
{
    protected ?string $directory = null;

    protected function base_path(): string
    {
        if (config('app.env') === 'testing') {
            return getcwd().'/module-test';
        }

        return getcwd();
    }

    protected function rootNamespace(): string
    {
        $composer = json_decode(
            (string) file_get_contents($this->base_path().'/composer.json'),
            true
        );

        if (! $namespace = array_search('src/', $composer['autoload']['psr-4'])) {
            throw new \RuntimeException('Unable to define namespace from composer.json');
        }

        return $namespace;
    }

    protected function getModuleInfos(?string $key = null): array|string
    {
        $composer = json_decode(
            (string) file_get_contents($this->base_path().'/composer.json'),
            true
        );

        $filename = collect($composer['autoload']['classmap'])
            ->filter(function (string $value) {
                return Str::endsWith($value, '.php');
            })
            ->first();

        if (is_null($filename)) {
            throw new \RuntimeException('Unable to find module main file in composer.json classmap');
        }

        $infos = [
            'name' => Str::chopEnd($filename, '.php'),
            'filename' => $filename,
        ];

        if (! is_null($key) && Arr::exists($infos, $key)) {
            return $infos[$key];
        }

        return $infos;
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
        return $this->base_path().'/src/'.$this->directory.'/'.$this->getNameInput().'.php';
    }
}
