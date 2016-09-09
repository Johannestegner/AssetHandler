<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Asset.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 9:06
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Jite\AssetHandler\Contracts\AssetContainerInterface;
use Jite\AssetHandler\Contracts\AssetInterface;
use Jite\AssetHandler\Exceptions\InvalidContainerException;

/**
 * @internal
 */
class Asset implements AssetInterface {

    const ASSET_TYPE_ANY = "any";

    /** @var string */
    private $type;

    /** @var string */
    private $path;

    /** @var string */
    private $name;

    /** @var AssetContainerInterface */
    private $container;

    /**
     * @param $type string
     * @param $path string
     * @param $name string
     * @param $container AssetContainerInterface|null
     */
    public function __construct(string $type, string $path, string $name, AssetContainerInterface $container = null) {

        $this->type      = $type;
        $this->path      = $path;
        $this->name      = $name;
        $this->container = $container;
    }

    /**
     * Get path to the asset (excludes name).
     *
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * Get name of asset.
     *
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    public function getFullUrl() {
        if (!$this->container) {
            throw new InvalidContainerException("Container was null.");
        }

        $baseUrl = $this->container->getBaseUrl();
        $prep    = $this->getPath();

        if (substr($baseUrl, -1) !== "/" && substr($baseUrl, -1) !== "\\") {
            $baseUrl .= "/";
        }

        if (substr($prep, 0, 1) === "/" || substr($prep, 0, 1) === "\\") {
            $prep = substr($prep, 1);
        }

        return $baseUrl . $prep;
    }

    /**
     * Get type of asset.
     *
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * Fetch the parent container.
     *
     * @return AssetContainerInterface|null
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * Set parent container.
     *
     * @param AssetContainerInterface $container
     */
    public function setContainer(AssetContainerInterface $container) {
        $this->container = $container;
    }
}
