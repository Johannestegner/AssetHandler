<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:21
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

interface AssetHandlerInterface {

    /**
     * Add an asset to the handler.
     *
     * Observe:
     * If no container is specified the handler will try to add it to a container depending on its file type.
     *
     * @param string $asset Asset path excluding the base path for given container.
     * @param string $assetName Asset name, Optional, if no name, the path will be used as name.
     * @param string $container Container name.
     * @return bool
     */
    public function add(string $asset, string $assetName = "", string $container = "any") : bool;

    /**
     * Remove an asset from the handler.
     *
     * Observe:
     * If no container is specified the handler will try to remove it from a container based on file type.
     *
     * @param string $assetName Asset name or path.
     * @param string $container
     * @return bool
     */
    public function remove(string $assetName, string $container = "any");

    /**
     * Print a single asset as a HTML tag.
     *
     * The handler will try to determine what type of tag to use by file type if no container is supplied.
     *
     * Observe:
     * Even though the container parameter is not required, it will be a faster lookup if the container is defined,
     * if it is not defined, the handler will look through all containers for the given asset.
     *
     * Please see the documentation for further information about the parameters.
     *
     * @param string $assetName Name of the asset or the asset path.
     * @param string $container Container for quicker access.
     * @param string $custom Custom tag.
     * @return string HTML formatted tag
     */
    public function print(string $assetName, string $container = "any", string $custom = "") : string;

    /**
     * Print all assets in a container (or all if none is supplied) as HTML tags.
     * The tags will be separated with a PHP_EOL char.
     *
     * @param string $container Container to print.
     * @return string HTML tags.
     */
    public function printAll(string $container = "any") : string;

    /**
     * Fetch all assets as a merged array of Asset objects.
     * If container is specified, only that containers assets will be returned, else all.
     *
     * @internal Usage of this method is not recommended.
     * @param string $container
     * @return AssetInterface[]|array
     */
    public function getAssets(string $container = "any") : array;

    /**
     * Set a container (or all if non is passed) to use versioning.
     * The versioning will add the files last modified time to the asset name on print.
     *
     * This is used to make sure that the asset is loaded when it has been edited (so that the browser cache don't
     * use an old asset).
     *
     * @param bool   $state
     * @param string $container
     * @return void
     */
    public function setIsUsingVersioning(bool $state, string $container = "any");

    /**
     * Check if a given container is using versioned assets.
     *
     * @param string $container
     * @return bool
     */
    public function isUsingVersioning(string $container) : bool;

    /**
     * Create a custom container.
     * The container will use the supplied tag format when creating a HTML tag.
     *
     * Please see the documentation for further information about the parameters.
     *
     * @param string $containerName Unique name for the new container.
     * @param string $customTag Custom tag (see docs above).
     * @param string $assetPath Base path for all assets in the container. Defaults to /public/assets
     * @param string $assetUrl Base URL for all assets. Defaults to /assets.
     * @param string $fileRegex Regex string in case the asset container should be able to auto determine assets types
     *                          by file name.
     * @return bool Result
     */
    public function addContainer(string $containerName, string $customTag, string $assetPath = "/public/assets",
                                 string $assetUrl = "/assets", string $fileRegex = null) : bool;

    /**
     * Remove a container.
     *
     * @param string $containerName Name of container to remove.
     * @return bool Result
     */
    public function removeContainer(string $containerName);

    /**
     * Set the base URL to a given container (or all).
     *
     * @param string $url URL to the public assets directory.
     * @param string $container
     * @return bool Result.
     */
    public function setBaseUrl(string $url = "/assets", string $container = "any") : bool;

    /**
     * Set the base path to a given container (or all).
     *
     * @param string $path Path to the assets folder.
     * @param string $container
     * @return bool Result.
     */
    public function setBasePath(string $path =  "public/assets", string $container = "any") : bool;
}
