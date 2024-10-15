<?php

use App\Services\Yaml\YamlConfigContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

it('can load routes.yml', function () {
    expect(
        app(YamlConfigContract::class)
            ->get('routes')
            ->data()
    )->toBeInstanceOf(Collection::class);
});

it('can register a route', function () {
    File::delete(getcwd().'/src/config/routes.yml');
    $yaml = app(YamlConfigContract::class)->get('routes');

    expect(
        $yaml->register([
            'route_name' => [
                'path' => '/module_name/route_path',
                'methods' => ['GET', 'POST'],
                'defaults' => [
                    '_controller' => 'PrestaShop\Module\ModuleName\Controller\ControllerNameController::index',
                    '_legacy_controller' => 'AdminModuleNameControllerName',
                    '_legacy_link' => 'AdminModuleNameControllerName',
                ],
            ],
        ])
    )->toBeTrue()
        ->and(
            $yaml->data()->toArray()
        )->toMatchArray([
            'route_name' => [
                'path' => '/module_name/route_path',
                'methods' => ['GET', 'POST'],
                'defaults' => [
                    '_controller' => 'PrestaShop\Module\ModuleName\Controller\ControllerNameController::index',
                    '_legacy_controller' => 'AdminModuleNameControllerName',
                    '_legacy_link' => 'AdminModuleNameControllerName',
                ],
            ],
        ]);
});
