<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandler.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:20
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

namespace Jite\AssetHandler;

use Jite\AssetHandler\Contracts\AssetHandlerInterface;
use Jite\AssetHandler\Exceptions\AssetNotFoundException;
use Jite\AssetHandler\Exceptions\InvalidAssetTypeException;

class AssetHandler implements AssetHandlerInterface {

    /** @var array */
    protected $assets = array();
    /** @var bool */
    protected $versioning;

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath = "public/assets") {
        $this->versioning = false;

        $this->assets = [
            self::ASSET_TYPE_SCRIPT => [
                "base_path" => $basePath,
                "assets" => []
            ],
            self::ASSET_TYPE_STYLE_SHEET => [
                "base_path" => $basePath,
                "assets" => []
            ]
        ];
    }

    /**
     * @param string $type
     * @return bool
     */
    private function assetTypeExists(string $type) {
        $result = array_key_exists($type, $this->assets);
        return $result;
    }

    /**
     * Add a style sheet path
     * @param string $asset
     * @return bool
     */
    public function addStyleSheet(string $asset) : bool {
        if (!in_array($asset, $this->assets[self::ASSET_TYPE_STYLE_SHEET]["assets"])) {
            $this->assets[self::ASSET_TYPE_STYLE_SHEET]["assets"][] = $asset;
            return true;
        }
        return false;
    }

    /**
     * Add a script path to the asset handler.
     * @param string $asset
     * @return bool
     */
    public function addScript(string $asset) : bool {
        if (!in_array($asset, $this->assets[self::ASSET_TYPE_SCRIPT]["assets"])) {
            $this->assets[self::ASSET_TYPE_SCRIPT]["assets"][] = $asset;
            return true;
        }
        return false;
    }

    /**
     * Set versioning of assets to on or off.
     * @param bool $value
     * @return void
     */
    public function setUseVersioning(bool $value) {
        $this->versioning = $value;
    }

    /**
     * Set the base path of a given asset type or all.
     * @param string $path
     * @param string $type
     * @return bool
     * @throws InvalidAssetTypeException
     * @throws AssetNotFoundException
     */
    public function setAssetBasePath(string $path, string $type = self::ASSET_TYPE_ALL) : bool {
        if (!$this->assetTypeExists($type)) {
            throw new InvalidAssetTypeException(sprintf("The asset type %s does not exist.", $type));
        }

        $last = substr($path, -1);
        if ($last !== "/" && $last !== '\\') {
            $path .= "/";
        }

        if ($path === self::ASSET_TYPE_ALL) {
            foreach ($this->assets as &$assetContainer) {
                $assetContainer['base_path'] = $path;
            }
        } else {
            $this->assets[$type]["base_path"] = $path;
        }

        // Check so that all the files actually exists.
        $assetsArray = array_map(function($container) {

                $basePath = $container['base_path'];
                $result   = array_map(function($path) use($basePath) {
                        return ['full' => $basePath . $path, 'internal' => $path];
                }, $container['assets']);

                return $result;

        }, $this->assets);

        $assets = array_merge(...$assetsArray);

        $first = array_first($assets, function($asset) {
            $result = file_exists($asset['full']);
            return !$result;
        });

        if ($first !== null) {
            $template = "Asset path (%s) could not be updated: Full path (%s) does not point to a valid file.";
            throw new AssetNotFoundException(sprintf($template, $first['internal'] ,$first['full']));
        };

        return true;
    }

    /**
     * Fetch the base path of a given asset type.
     * @param string $type
     * @return string
     * @throws InvalidAssetTypeException
     */
    public function getAssetBasePath(string $type) : string {
        if ($type === self::ASSET_TYPE_ALL) {
            $path = $$this->assets[self::ASSET_TYPE_SCRIPT]['base_path'];

            foreach ($this->assets as $assetContainer) {
                if ($assetContainer['base_path'] !== $path) {
                    throw new InvalidAssetTypeException("Can not fetch the asset base path: Assets base path differs.");
                }
            }
        }

        if (!$this->assetTypeExists($type)) {
            throw new InvalidAssetTypeException(sprintf("The asset type %s does not exist.", $type));
        }

        return $this->assets[$type]['base_path'];
    }

    /**
     * Get all assets of a given type or all.
     * @param string $type
     * @return array All assets of given type or a concatenated array of all assets.
     * @throws InvalidAssetTypeException
     */
    public function getAssets(string $type = self::ASSET_TYPE_ALL) : array {
        if (!$this->assetTypeExists($type)) {
            throw new InvalidAssetTypeException(sprintf("The asset type %s does not exist.", $type));
        }

        $result = array();
        if ($type === self::ASSET_TYPE_ALL) {
            foreach ($this->assets as $assetContainer) {
                $result = array_merge($result, $assetContainer['assets']);
            }
        } else {
            $result = $this->assets[$type]['assets'];
        }

        return $result;
    }

    /**
     * Prints the scripts for html output, including script tags.
     * @return string
     */
    public function scripts() : string {
        $scripts           = $this->getAssets(self::ASSET_TYPE_SCRIPT);
        $out               = "";
        $scriptTagTemplate = '<script type="text/javascript" src="%s"></script>' . PHP_EOL;


        // Due to the fact that we check if the asset exists when we add it to the asset container,
        // we don't need to check that here.
        // If the asset has been moved for some odd reason, the asset will not be found when loaded,
        // no exception will be thrown in the php code.
        foreach ($scripts as $scriptPath) {
            $out .= sprintf($scriptTagTemplate, $scriptPath);
        }

        return $out;
    }

    /**
     * Prints the styles for html output, including style tag.
     * @return string
     */
    public function styles() : string {
        $styles           = $this->getAssets(self::ASSET_TYPE_STYLE_SHEET);
        $out              = "";
        $styleTagTemplate = '<link rel="stylesheet" href="%s">' . PHP_EOL;

        // Due to the fact that we check if the asset exists when we add it to the asset container,
        // we don't need to check that here.
        // If the asset has been moved for some odd reason, the asset will not be found when loaded,
        // no exception will be thrown in the php code.
        foreach ($styles as $stylePath) {
            $out .= sprintf($styleTagTemplate, $stylePath);
        }

        return $out;
    }

    /**
     * Get the full path (including the base path) to a given asset.
     * @param string $asset
     * @param string $assetType Type of asset, defined as constants in the class.
     * @return string
     * @throws InvalidAssetTypeException
     */
    public function getAssetPath(string $asset, string $assetType) : string {
        if (!$this->assetTypeExists($assetType)) {
            throw new InvalidAssetTypeException(sprintf("The asset type %s does not exist.", $assetType));
        }

        $basePath = $this->getAssetBasePath($assetType);
        return $basePath . $asset;
    }
}
