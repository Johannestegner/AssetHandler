<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  ContainerData.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-29 - kl 20:23
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Jite\AssetHandler\Contracts\AssetContainerInterface as ACI;

/**
 * @internal
 */
class ContainerData {

    private $fileRegex    = null;
    private $printPattern = null;
    private $type         = null;
    private $path         = null;
    private $container    = null;

    /**
     * @param ACI $container
     * @param                         $type
     * @param                         $path
     * @param                         $printPattern
     * @param null                    $fileRegex
     */
    public function __construct(ACI $container, $type, $path, $printPattern, $fileRegex = null) {
        $this->container    = $container;
        $this->type         = $type;
        $this->path         = $path;
        $this->printPattern = $printPattern;
        $this->fileRegex    = $fileRegex;
    }

    /**
     * @return ACI
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * @return string|null
     */
    public function getFileRegex() {
        return $this->fileRegex;
    }

    /**
     * @param string|null $fileRegex
     */
    public function setFileRegex($fileRegex) {
        $this->fileRegex = $fileRegex;
    }

    /**
     * @return string|null
     */
    public function getPrintPattern() {
        return $this->printPattern;
    }

    /**
     * @param string|null $printPattern
     */
    public function setPrintPattern($printPattern) {
        $this->printPattern = $printPattern;
    }

    /**
     * @return string|null
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

}
