<?php

namespace App\Providers;

use App\Services\Yaml\RoutesYaml;
use App\Services\Yaml\ServicesYaml;
use App\Services\Yaml\YamlConfigContract;
use App\Services\Yaml\YamlConfigService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(YamlConfigService::class, function (Application $app, array $parameters = []) {
            return new YamlConfigService(...$parameters);
        });

        $this->app->bind(YamlConfigContract::class, function () {
            return collect([
                'routes' => app(RoutesYaml::class),
                'services' => app(ServicesYaml::class),
            ]);
        });
    }
}
