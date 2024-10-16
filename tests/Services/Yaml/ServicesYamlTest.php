<?php

use App\Services\Yaml\YamlConfigContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

it('can load services.yml', function () {
    expect(
        app(YamlConfigContract::class)
            ->get('services')
            ->data()
    )->toBeInstanceOf(Collection::class);
});

it('can register a service', function () {
    File::delete(getcwd().'/module-test/config/services.yml');
    $yaml = app(YamlConfigContract::class)->get('services');

    expect(
        $yaml->register([
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
        ])
    )->toBeTrue()
        ->and(
            $yaml->data()->toArray()
        )->toMatchArray([
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

    $yaml->register([
        'prestashop.module.ibrostudio.form.configuration_form_data_handler2' => [
            'class' => 'PrestaShop\PrestaShop\Core\Form\Handler',
        ],
    ]);

    expect(
        $yaml->data()->toArray()
    )->toMatchArray([
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
            'prestashop.module.ibrostudio.form.configuration_form_data_handler2' => [
                'class' => 'PrestaShop\PrestaShop\Core\Form\Handler',
            ],
        ],
    ]);
});
