<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandler.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:20
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

namespace Jite\AssetHandler;

use Jite\AssetHandler\Contracts\AssetHandlerInterface;
use Jite\AssetHandler\Types\AssetTypes;
use Jite\AssetHandler\Exceptions\ {
    AssetNameNotUniqueException,
    InvalidAssetException,
    InvalidContainerException,
    InvalidPathException,

    ExceptionMessages as Errors
};

class AssetHandler implements AssetHandlerInterface {



    // Should later be removed and replaced with a config.
    private $knownTypes = array(
        AssetTypes::STYLE_SHEET => [
            "regex"        => "/\\.css$/i",
            "print_string" => '<link rel="stylesheet" type="text/css" href="{{PATH}}" title="{{NAME}}">'
        ],
        AssetTypes::SCRIPT => [
            "regex"        => "/\\.js$/i",
            "print_string" => '<script src="{{PATH}}" type="application/javascript"></script>'
        ],
        AssetTypes::IMAGE => [
            "regex"        => "/\\.(jpg|jpeg|tiff|gif|png|bmp|ico)$/i",
            "print_string" => '<img src="{{PATH}}">'
        ]
    );

    /** @var AssetContainer[] */
    private $containers = array();
    private $paths      = array();

    public function __construct(string $basePath = null) {
        foreach (AssetTypes::getTypes() as $type) {
            if ($type === AssetTypes::ANY) {
                continue;
            }

            $this->containers[$type] = new AssetContainer();
            $this->paths[$type]      = null;
        }

        $this->setBasePath($basePath, AssetTypes::ANY);
    }

    private function containerExists(string $container) : bool {
        if (!array_has($this->containers, $container)) {
            return false;
        }
        return true;
    }

    private function determineContainer(string $fileName) : string {
        foreach ($this->knownTypes as $type => $data) {
            if (!isset($data['regex'])) {
                continue;
            }

            if (preg_match($data['regex'], $fileName) === 1) {
                return $type;
            }
        }
        return "";
    }

    /**
     * @param string $assetName
     * @param        $containers
     * @return Asset|null
     */
    private function findAssetByName(string $assetName, $containers) {
        foreach ($containers as $cType) {
            $result = $this->containers[$cType]->find(function(Asset $asset) use($assetName) {
                $result = $asset->getName() === $assetName;
                return $result;
            });

            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }

    /**
     * @param string $assetPath
     * @param        $containers
     * @return Asset|null
     */
    private function findAssetByPath(string $assetPath, $containers) {
        foreach ($containers as $cType) {
            $result = $this->containers[$cType]->find(function(Asset $asset) use($assetPath) {
                $result = $asset->getPath() === $assetPath;
                return $result;
            });

            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }

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
     * @throws AssetNameNotUniqueException
     * @throws InvalidContainerException
     */
    public function add(string $asset, string $assetName = "", string $container = AssetTypes::ANY) : bool {

        $assetName = $assetName === "" ? $asset : $assetName;

        if ($container === AssetTypes::ANY) {
            $container = $this->determineContainer($asset);
            if ($container === "") {
                throw new InvalidContainerException(sprintf(Errors::CONTAINER_NOT_DETERMINABLE, $asset));
            }
        }

        if (!$this->containerExists($container)) {
            throw new InvalidContainerException(sprintf(Errors::CONTAINER_NOT_EXIST, $container));
        }

        $exists = $this->findAssetByName($assetName, [$container]);

        if ($exists !== null) {
            throw new AssetNameNotUniqueException(sprintf(Errors::ASSET_NOT_CONTAINER_UNIQUE, $assetName, $container));
        }

        $this->containers[$container]->add(new Asset($container, $asset, $assetName));
        return true;
    }

    /**
     * Remove an asset from the handler.
     *
     * Observe:
     * If no container is specified the handler will try to remove it
     * from a container based on the file type. If file type can not be determined a exception will be thrown.
     * Its always recommended to use a container when calling this method.
     *
     * @param string $assetName Asset name or path.
     * @param string $container
     * @return bool
     * @throws AssetNameNotUniqueException
     * @throws InvalidAssetException
     * @throws InvalidContainerException
     */
    public function remove(string $assetName, string $container = AssetTypes::ANY) {

        if ($container === AssetTypes::ANY) {
            $container = $this->determineContainer($assetName);

            if ($container === "") {
                throw new InvalidContainerException(sprintf(Errors::CONTAINER_NOT_DETERMINABLE, $assetName));
            }
        }

        if (!$this->containerExists($container)) {
            throw new InvalidContainerException(sprintf(Errors::CONTAINER_NOT_EXIST, $container));
        }

        $has = $this->findAssetByPath($assetName, [$container]) ?? $this->findAssetByName($assetName, [$container]);
        return $has === null ? false : $this->containers[$container]->remove($has);
    }

    /**
     * Print all assets in a container (or all if none is supplied) as HTML tags.
     * The tags will be separated with a PHP_EOL char.
     *
     * @param string $container Container to print.
     * @return string HTML tags.
     * @throws InvalidContainerException
     */
    public function printAll(string $container = AssetTypes::ANY) : string {

        $containers = [];
        if ($container !== AssetTypes::ANY) {
            $containers[] = $container;
        } else {
            foreach ($this->containers as $key => $val) {
                $containers[] = $key;
            }
        }

        $out = "";
        foreach ($containers as $container) {

            foreach ($this->containers[$container] as $asset) {

                if (array_key_exists($container, $this->knownTypes)) {
                    $pattern = $this->knownTypes[$container]["print_string"];
                } else {
                    throw new InvalidContainerException(sprintf(Errors::PRINT_PATTERN_MISSING, $container));
                }

                /** @var Asset $asset */
                $pattern = str_replace("{{PATH}}", $asset->getFullUrl(), $pattern);
                $out    .= str_replace("{{NAME}}", $asset->getName(), $pattern) . PHP_EOL;
            }
        }

        return $out;
    }

    /**
     * Fetch all assets as a merged array of Asset objects.
     * If container is specified, only that containers assets will be returned, else all.
     *
     * @internal
     * @param string $container
     * @return Asset[]|array
     */
    public function getAssets(string $container = AssetTypes::ANY) : array {
        $containers = [];

        if ($container === AssetTypes::ANY) {
            $containers = array_keys($this->containers);
            $containers = array_map(function(string $container) {
                return $this->containers[$container]->toArray();
            }, $containers);
        } else {
            $containers[] = $this->containers[$container]->toArray();
        }

        return array_merge(... $containers);
    }

    /**
     * Set a container (or all if non is passed) to use versioning.
     * The versioning will add the files last modified time to the asset name on print.
     *
     * @param bool   $state
     * @param string $container
     * @return void
     */
    public function setIsUsingVersioning(bool $state, string $container = AssetTypes::ANY) {
        // TODO: Implement setIsUsingVersioning() method.
    }

    /**
     * Remove a custom container (the predefined containers will not be possible to remove).
     *
     * @param string $containerName Name of container to remove.
     * @return bool Result
     * @throws InvalidContainerException
     */
    public function removeContainer(string $containerName) {
        if (!$this->containerExists($containerName)) {
            throw new InvalidContainerException(Errors::CONTAINER_NOT_EXIST, $containerName);
        }

        unset($this->containers[$containerName]);
        unset($this->knownTypes[$containerName]);
        unset($this->paths[$containerName]);

        return true;
    }

    /**
     * Set the base URL to a given container (or all).
     *
     * @param string $url URL to the public assets directory.
     * @param string $container
     * @return bool Result.
     * @throws InvalidContainerException
     */
    public function setBaseUrl(string $url = "/assets", string $container = AssetTypes::ANY) : bool {

        if ($container === AssetTypes::ANY) {
            foreach ($this->containers as $container) {
                $container->setBaseUrl($url);
            }
            return true;
        }

        if (!array_key_exists($container, $this->containers)) {
            throw new InvalidContainerException(sprintf(Errors::CONTAINER_NOT_EXIST, $container));
        }

        $this->containers[$container]->setBaseUrl($url);
        return true;
    }

    /**
     * Set the base path to a given container (or all).
     *
     * @param string $path Path to the assets folder.
     * @param string $container
     * @return bool
     * @throws InvalidContainerException
     * @throws InvalidPathException
     */
    public function setBasePath(string $path = null, string $container = AssetTypes::ANY) : bool {
        // When setting a path, the bundle needs to access the filesystem to check that the path is actually real.
        // If path is set to null, there will be no FS access.
        $containers = [];

        if ($container === AssetTypes::ANY) {
            $containers = array_keys($this->containers);
        } else {
            if (!array_key_exists($container, $this->containers)) {
                throw new InvalidContainerException(sprintf(Errors::CONTAINER_NOT_EXIST, $container));
            }
            $containers = [$container];
        }

        // Check path.
        if ($path !== null && !is_dir($path)) {
            throw new InvalidPathException(sprintf(Errors::INVALID_PATH, $path));
        }


        foreach ($containers as $container) {
            $this->paths[$container] = $path;
        }

        return true;
    }

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
     * @throws InvalidContainerException
     */
    public function print(string $assetName, string $container = AssetTypes::ANY, string $custom = "") : string {
        $containers = [];

        if ($container === AssetTypes::ANY) {
            // Try determine container.
            $container = $this->determineContainer($assetName);
            if (!$container) {
                $containers = array_keys($this->containers);
            } else {
                $containers[] = $container;
            }
        } else {
            $containers[] = $container;
        }

        // Check each container in the array and try find asset by name.

        $exists = $this->findAssetByName($assetName, $containers) ?? $this->findAssetByPath($assetName, $containers);

        if (!$exists) {
            return "<!-- Failed to fetch asset ({$assetName}) -->" . PHP_EOL;
        }

        // If custom is set, that is what is supposed to be used.
        // Else the knownTypes array have to contain a pattern for it to work.
        // If it does not, its quite fatal!
        $pattern = $custom;
        if ($pattern === "") {
            if (array_key_exists($exists->getType(), $this->knownTypes)) {
                $pattern = $this->knownTypes[$exists->getType()]["print_string"];
            } else {
                throw new InvalidContainerException(Errors::PRINT_PATTERN_MISSING, $exists->getType());
            }
        }

        // Replace the placeholders.
        $pattern = str_replace("{{PATH}}", $exists->getFullUrl(), $pattern);
        return str_replace("{{NAME}}", $exists->getName(), $pattern) . PHP_EOL;
    }

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
     * @throws InvalidContainerException
     */
    public function addContainer(string $containerName, string $customTag) : bool {

        if ($this->containerExists($containerName)) {
            throw new InvalidContainerException(Errors::CONTAINER_NOT_UNIQUE, $containerName);
        }

        $this->containers[$containerName] = new AssetContainer("");
        $this->knownTypes[$containerName] = [ "print_string" => $customTag ];
        return true;
    }
}
