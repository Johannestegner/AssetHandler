<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:21
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

use Jite\AssetHandler\Types\AssetTypes;

interface AssetHandlerInterface {

    /**
     * Add an asset to the handler.
     *
     * Observe:
     * If no container is specified the handler will add it to a predefined container depending on its file type.
     *
     * @param string $asset Asset path excluding the base path for given container.
     * @param string $assetName Asset name, Optional, if no name, the path will be used as name.
     * @param string $container Container name.
     * @return bool
     */
    public function add(string $asset, string $assetName = "", string $container = AssetTypes::ANY) : bool;

    /**
     * Remove an asset from the handler.
     *
     * Observe:
     * If no container is specified the handler will try to remove it from a predefined container based on file type.
     * If no asset is found in the predefined container, none will be removed.
     *
     * @param string $assetName Asset name or path.
     * @param string $container
     * @return bool
     */
    public function remove(string $assetName, string $container = AssetTypes::ANY);

    /**
     * Print a single asset as a HTML tag.
     *
     * The handler will try to determine what type of tag to use by file type if no container is supplied.
     * The predefined containers (ex. Script and Style sheet) will use the standard tags.
     * If no asset is found in any container, a HTML comment will be produced instead:
     * <!-- Failed to fetch asset (/asset/path) -->
     *
     * Observe:
     * Even though the container parameter is not required, it will be a faster lookup if the container is defined,
     * if it is not defined, the handler will look through all containers for the given asset.
     *
     * Custom Tag:
     * The custom tag uses a very simple template system, where two arguments will be possible to pass:
     * NAME and PATH.
     * The arguments in the string should be enclosed by {{ARGUMENT}} to be printed, example:
     * <script src="{{PATH}}"></script>
     * Will print:
     * <script src="/some/path/to/file.js"></script>
     *
     * @param string $assetName Name of the asset or the asset path.
     * @param string $container Container for quicker access.
     * @param string $custom Custom tag.
     * @return string HTML formatted tag
     */
    public function print(string $assetName, string $container = AssetTypes::ANY, string $custom = "") : string;

    /**
     * Print all assets in a container (or all if none is supplied) as HTML tags.
     * The tags will be separated with a PHP_EOL char.
     *
     * @param string $container Container to print.
     * @return string HTML tags.
     */
    public function printAll(string $container = AssetTypes::ANY) : string;

    /**
     * Fetch all assets as a merged array of Asset objects.
     * If container is specified, only that containers assets will be returned, else all.
     *
     * @internal
     * @param string $container
     * @return AssetInterface[]|array
     */
    public function getAssets(string $container = AssetTypes::ANY) : array;

    /**
     * Set a container (or all if non is passed) to use versioning.
     * The versioning will add the files last modified time to the asset name on print.
     *
     * @param bool   $state
     * @param string $container
     * @return void
     */
    public function setIsUsingVersioning(bool $state, string $container = AssetTypes::ANY);

    /**
     * Create a custom container.
     * The container will use the supplied tag format when creating a HTML tag.
     *
     * Custom Tag:
     * The custom tag uses a very simple template system, where two arguments will be possible to pass:
     * NAME and PATH.
     * The arguments in the string should be enclosed by {{ARGUMENT}} to be printed, example:
     * <script src="{{PATH}}"></script>
     * Will print:
     * <script src="/some/path/to/file.js"></script>
     *
     * @param string $containerName Unique name for the new container.
     * @param string $customTag Custom tag (see docs above).
     * @return bool Result
     */
    public function addContainer(string $containerName, string $customTag) : bool;

    /**
     * Remove a custom container (the predefined containers will not be possible to remove).
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
     * @return void
     */
    public function setBaseUrl(string $url = "/assets", string $container = AssetTypes::ANY);

    /**
     * Set the base path to a given container (or all).
     *
     * @param string $path Path to the assets folder.
     * @param string $container
     * @return void
     */
    public function setBasePath(string $path =  "public/assets", string $container = AssetTypes::ANY);
}
