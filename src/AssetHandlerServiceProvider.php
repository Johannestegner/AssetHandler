<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerServiceProvider.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:14
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\AssetHandler;

use Illuminate\Support\ServiceProvider;

class AssetHandlerServiceProvider extends ServiceProvider {

    public function register() {
        $configPath = __DIR__ . '/../config/asset-handler.php';
        $this->mergeConfigFrom($configPath, 'asset-handler');
    }

    public function boot() {
        $config = __DIR__ . '/../config/AssetHandler.php';
        /** @noinspection PhpUndefinedFunctionInspection */
        $publish = base_path('config/asset-handler.php');

        if (function_exists('config_path')) {
            $publish = config_path('asset-handler.php');
        }

        $this->publishes([$config => $publish], 'config');
    }
}
