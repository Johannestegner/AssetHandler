<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerFacade.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-09-11 - kl 19:21
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace JohannesTegner\AssetHandler;

use Illuminate\Support\Facades\Facade;

class AssetHandlerFacade extends Facade {

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() {
        return AssetHandler::class;
    }
}
