<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandler.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-08 - kl 15:20
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

namespace Jite\AssetHandler;

use Jite\AssetHandler\Contracts\AssetHandlerInterface;
use Jite\AssetHandler\Exceptions\AssetNameNotUniqueException;
use Jite\AssetHandler\Exceptions\InvalidAssetException;
use Jite\AssetHandler\Exceptions\InvalidContainerException;
use Jite\AssetHandler\Types\AssetTypes;

class AssetHandler implements AssetHandlerInterface {

    private $knownTypes = array(
        AssetTypes::STYLE_SHEET => "/\\.css$/i",
        AssetTypes::SCRIPT => "/\\.js$/i",
        AssetTypes::IMAGE => "/\\.(jpg|jpeg|tiff|gif|png|bmp|ico)$/i"
    );

    /** @var AssetContainer[] */
    private $containers = array();

    public function __construct() {
        foreach (AssetTypes::getTypes() as $type) {
            if ($type === AssetTypes::ANY) {
                continue;
            }

            $this->containers[$type] = new AssetContainer();
        }
    }

    private function containerExists(string $container) : bool {
        if (!array_has($this->containers, $container)) {
            return false;
        }
        return true;
    }

    private function determineContainer(string $fileName) : string {
        foreach ($this->knownTypes as $type => $pattern) {
            if (preg_match($pattern, $fileName) === 1) {
                return $type;
            }
        }
        return "";
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
                $msg = sprintf("Could not determine Container from the asset path (%s).", $asset);
                throw new InvalidContainerException($msg);
            }
        }

        if (!$this->containerExists($container)) {
            $msg = sprintf('No container with name "%s" found in the asset handler.', $container);
            throw new InvalidContainerException($msg);
        }

        $exists = $this->containers[$container]->find(function(Asset $asset) use($assetName) {
            $result = $asset->getName() === $assetName;
            return $result;
        });

        if ($exists !== null) {
            $msg = sprintf("An asset with the name %s already exists in the container (%s)", $assetName, $container);
            throw new AssetNameNotUniqueException($msg);
        }

        $this->containers[$container]->add(new Asset($container, $asset, $assetName));

        return true;
    }

    /**
     * Remove an asset from the handler.
     *
     * Observe:
     * If no container is specified the handler will try to remove it
     * from a predefined container based on the file type.
     * If no asset is found in the predefined container, none will be removed.
     *
     * @param string $assetName Asset name or path.
     * @param string $container
     * @return bool
     * @throws AssetNameNotUniqueException
     * @throws InvalidAssetException
     * @throws InvalidContainerException
     */
    public function remove(string $assetName, string $container = AssetTypes::ANY) {


        if ($container === AssetTypes::ANY && str_contains($assetName, ".")) {
            $container = $this->determineContainer($assetName);

            if ($container === "") {
                $msg = sprintf(
                    'Failed to remove asset with name "%s". The asset container could not be determined from file type.'
                    , $assetName
                );
                throw new InvalidAssetException($msg);
            }
        } else if ($container === AssetTypes::ANY) {
            // Not possible to determine a filename from a file without a file type (no dot!), so
            // make sure that the asset does not exist in multiple containers before removing any.
            // Check only predefined, if not in one of those, just skip it and blow up!
            $types = array_filter(AssetTypes::getTypes(), function(string $type) {
                return $type !== AssetTypes::ANY;
            });

            $container = null;
            $out       = array_where($types, function(int $index) use($assetName, $types, &$container) {

                $exists = $this->containers[$types[$index]]->find(function(Asset $asset) use($assetName) {
                    $result = $asset->getName() === $assetName;
                    return $result;
                });

                if ($exists) {
                    $container = $types[$index];
                    return true;
                }
                return false;
            });

            if (count($out) > 1) {
                $msg = sprintf(
                    'Failed to remove asset with name "%s". ' .
                    'Due to none unique name, the container name is required for this operation.',
                    $assetName
                );
                throw new AssetNameNotUniqueException($msg);
            } else if (count($out) <= 0) {
                return false;
            }
        }

        if (!$this->containerExists($container)) {
            $msg = sprintf(
                'Failed to remove asset with name "%s". The container (%s) does not exist.',
                $assetName,
                $container
            );
            throw new InvalidContainerException($msg);
        }

        $has = $this->containers[$container]->find(function(Asset $asset) use($assetName) {
            $res = $asset->getPath() === $assetName;
            return $res;
        }) ?? $this->containers[$container]->find(function(Asset $asset) use($assetName) {
            $res = $asset->getName() === $assetName;
            return $res;
        });

        $res = $has === null ? false : $this->containers[$container]->remove($has);
        return $res;
    }

    /**
     * Print a single asset as a HTML tag.
     *
     * The handler will try to determine what type of tag to use by file type/container.
     * The predefined containers (ex. Script and Style sheet) will use the standard tags.
     *
     * Observe:
     * Even though the container parameter is not required, it will be a faster lookup if the container is defined,
     * if it is not defined, the handler will look through all containers for the given asset.
     *
     * @param string $assetName Name of the asset or the asset path.
     * @param string $container Container for quicker access.
     * @param string $custom Custom tag format in printf format, strings passed will be: 1 asset url, 2 asset name.
     * @return string HTML formatted tag
     */
    public function print(string $assetName, string $container = AssetTypes::ANY, string $custom = "") : string {
        // TODO: Implement print() method.
    }

    /**
     * Print all assets in a container (or all if none is supplied) as HTML tags.
     * The tags will be separated with a PHP_EOL char.
     *
     * @param string $container Container to print.
     * @return string HTML tags.
     */
    public function printAll(string $container = AssetTypes::ANY) : string {
        // TODO: Implement printAll() method.
    }

    /**
     * Fetch all assets as a merged array of strings (full url).
     * If container is specified, only that containers assets will be returned.
     *
     * @param string $container
     * @return array
     */
    public function getAssets(string $container = AssetTypes::ANY) : array {
        $containers = array();
        if ($container === AssetTypes::ANY) {

            foreach (AssetTypes::getTypes() as $type) {
                if ($type === AssetTypes::ANY) {
                    continue;
                }

                $containers[] = $type;
            }
        } else {
            $containers[] = $container;
        }

        $result = array();
        foreach ($containers as $containerName) {
            $container = $this->containers[$containerName];
            foreach ($container->getIterator() as $asset) {
                /** @var $asset Asset */
                $result[] = $asset->getFullPath();
            }
        }

        return $result;
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
     * Create a custom container.
     * The container will use the supplied tag format when creating a HTML tag.
     *
     * @param string $containerName Unique name for the new container.
     * @param string $tagFormat Tag format string in printf format, strings passed will be: 1 asset url, 2 asset name.
     * @return bool Result
     */
    public function addContainer(string $containerName, string $tagFormat) : bool {
        // TODO: Implement addContainer() method.
    }

    /**
     * Remove a custom container (the predefined containers will not be possible to remove).
     *
     * @param string $containerName Name of container to remove.
     * @return bool Result
     */
    public function removeContainer(string $containerName) {
        // TODO: Implement removeContainer() method.
    }

    /**
     * Set the base URL to a given container (or all).
     *
     * @param string $url URL to the public assets directory.
     * @param string $container
     * @return void
     */
    public function setBaseUrl(string $url = "/assets", string $container = AssetTypes::ANY) {
        // TODO: Implement setBaseUrl() method.
    }

    /**
     * Set the base path to a given container (or all).
     *
     * @param string $path Path to the assets folder.
     * @param string $container
     * @return void
     */
    public function setBasePath(string $path = "public/assets", string $container = AssetTypes::ANY) {
        // TODO: Implement setBasePath() method.
    }
}
