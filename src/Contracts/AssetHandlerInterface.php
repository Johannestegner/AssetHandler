<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:21
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

use Jite\AssetHandler\Exceptions\AssetNotFoundException;
use Jite\AssetHandler\Exceptions\InvalidAssetTypeException;

interface AssetHandlerInterface {

    const ASSET_TYPE_SCRIPT      = "script";
    const ASSET_TYPE_STYLE_SHEET = "style sheet";
    const ASSET_TYPE_ALL         = "all";

    /**
     * Add a script path to the asset handler.
     * @param string $asset
     * @return bool
     * @throws AssetNotFoundException
     */
    public function addScript(string $asset) : bool;

    /**
     * Add a style sheet path
     * @param string $asset
     * @return bool
     * @throws AssetNotFoundException
     */
    public function addStyleSheet(string $asset) : bool;

    /**
     * Get the full path (including the base path) to a given asset.
     * @param string $asset
     * @param string $assetType Type of asset, defined as constants in the class.
     * @return string
     * @throws InvalidAssetTypeException
     */
    public function getAssetPath(string $asset, string $assetType) : string;

    /**
     * Set the base path of a given asset type or all.
     * @param string $path
     * @param string $type
     * @return bool
     * @throws InvalidAssetTypeException
     * @throws AssetNotFoundException
     */
    public function setAssetBasePath(string $path, string $type = self::ASSET_TYPE_ALL) : bool;

    /**
     * Fetch the base path of a given asset type.
     * @param string $type
     * @return string
     * @throws InvalidAssetTypeException
     */
    public function getAssetBasePath(string $type) : string;

    /**
     * Get all assets of a given type or all.
     * @param string $type
     * @return array All assets of given type or a concatenated array of all assets.
     * @throws InvalidAssetTypeException
     */
    public function getAssets(string $type = self::ASSET_TYPE_ALL) : array;

    /**
     * Prints the scripts for html output, including script tags.
     * @return string
     */
    public function scripts() : string;

    /**
     * Prints the styles for html output, including style tag.
     * @return string
     */
    public function styles() : string;

    /**
     * Set versioning of assets to on or off.
     * @param bool $value
     * @return void
     */
    public function setUseVersioning(bool $value);
}
