<?php

use App\Services\Yaml\YamlConfigService;

it('can load a yaml file', function () {
    expect(
        app(YamlConfigService::class)
            ->load(getcwd().'/src/config/control.yml')
            ->data
            ->toArray()
    )->toMatchArray([
        'imports' => [
            [
                'resource' => './common.yml',
            ],
        ],
        'services' => [
            '_defaults' => [
                'public' => true,
            ],
            'prestashop.module.ibrostudio.form.configuration_form_data_handler' => [
                'class' => 'PrestaShop\PrestaShop\Core\Form\Handler',
                'arguments' => [
                    '@form.factory',
                    '@prestashop.core.hook.dispatcher',
                ],
                'public' => true,
                'tags' => [
                    [
                        'name' => 'form.type',
                    ],
                ],
            ],
        ],
    ]);
});

it('can write a yaml file', function () {
    $yaml = app(YamlConfigService::class)
        ->load(getcwd().'/src/config/control.yml');
    $yaml->write(getcwd().'/src/config/test.yml');

    expect(
        $yaml->load(getcwd().'/src/config/test.yml')
            ->data
            ->toArray()
    )->toMatchArray([
        'imports' => [
            [
                'resource' => './common.yml',
            ],
        ],
        'services' => [
            '_defaults' => [
                'public' => true,
            ],
            'prestashop.module.ibrostudio.form.configuration_form_data_handler' => [
                'class' => 'PrestaShop\PrestaShop\Core\Form\Handler',
                'arguments' => [
                    '@form.factory',
                    '@prestashop.core.hook.dispatcher',
                ],
                'public' => true,
                'tags' => [
                    [
                        'name' => 'form.type',
                    ],
                ],
            ],
        ],
    ]);
});
