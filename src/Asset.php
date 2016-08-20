<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Asset.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 9:06
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Jite\AssetHandler\Contracts\AssetInterface;

class Asset implements AssetInterface {

    /** @var string */
    private $type;

    /** @var string */
    private $path;

    /** @var string */
    private $name;

    /**
     * @param string $type
     * @param string $path
     * @param string $name
     */
    public function __construct(string $type, string $path, string $name) {

        $this->type = $type;
        $this->path = $path;
        $this->name = $name;
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

    /**
     * Full path of the asset, including name.
     *
     * @return string
     */
    public function getFullPath() : string {
        if (substr($this->path, -1) !== "/" || substr($this->path, -1) !== "\\") {
            return $this->path . "/" . $this->name;
        }
        return $this->path . $this->name;
    }

    /**
     * Get type of asset.
     *
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }
}
