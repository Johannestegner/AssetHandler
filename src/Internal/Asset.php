<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Asset.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 9:06
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace JohannesTegner\AssetHandler\Internal;

use JohannesTegner\AssetHandler\Internal\Contracts\AssetContainerInterface;
use JohannesTegner\AssetHandler\Internal\Contracts\AssetInterface;
use JohannesTegner\AssetHandler\Internal\Exceptions\InvalidContainerException;

/**
 * @internal
 */
class Asset implements AssetInterface {

    const ASSET_TYPE_ANY = "any";

    /** @var string */
    private $path;

    /** @var string */
    private $name;

    /** @var AssetContainer */
    private $container;

    /**
     * @param $path string
     * @param $name string
     * @param $container AssetContainerInterface|null
     */
    public function __construct(string $path, string $name, AssetContainerInterface $container = null) {

        $this->path      = $path;
        $this->name      = $name;
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getType() : string {
        return $this->getContainer()->getType();
    }

    /**
     * @inheritdoc
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * @inheritdoc
     */
    public function setContainer(AssetContainerInterface $container) {
        $this->container = $container;
    }
}
