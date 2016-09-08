<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerServiceProvider.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:14
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Illuminate\Support\ServiceProvider;

class AssetHandlerServiceProvider extends ServiceProvider {

    public function register() {
        $configPath = __DIR__ . '/../config/AssetHandler.php';
        $this->mergeConfigFrom($configPath, 'AssetHandler');
    }

    public function boot() {
        $config = __DIR__ . '/../config/AssetHandler.php';
        /** @noinspection PhpUndefinedFunctionInspection */
        $publish = base_path('config/AssetHandler.php');

        if (function_exists('config_path')) {
            $publish = config_path('AssetHandler.php');
        }

        $this->publishes([$config => $publish], 'config');
    }
}
