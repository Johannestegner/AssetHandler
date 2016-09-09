<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetContainer.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-18 - kl 10:27
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Jite\AssetHandler\Contracts\AssetContainerInterface;
use Jite\AssetHandler\Contracts\AssetInterface;
use Jite\AssetHandler\Contracts\ContainerDataInterface;

/**
 * @internal
 */
class AssetContainer implements AssetContainerInterface, ContainerDataInterface {

    private $innerContainer = array();
    private $count          = 0;
    private $baseUrl        = "";
    private $basePath       = "";
    private $type           = "";
    private $printPattern   = "";
    private $fileRegex      = "";


    /**
     * @param string      $type
     * @param string      $baseUrl
     * @param string      $basePath
     * @param string|null $printPattern
     * @param string|null $fileRegex
     */
    public function __construct(string $type,
                                string $baseUrl = "/assets",
                                string $basePath = "/public/assets",
                                string $printPattern = null,
                                string $fileRegex = null) {

        $this->type         = $type;
        $this->baseUrl      = $baseUrl;
        $this->basePath     = $basePath;
        $this->printPattern = $printPattern;
        $this->fileRegex    = $fileRegex;
    }

    /**
     * @inheritDoc
     */
    public function add(AssetInterface $asset) : bool {
        // Check if asset already in the container.
        if ($this->exists($asset)) {
            return false;
        }
        $asset->setContainer($this);

        $this->innerContainer[$this->count] = $asset;
        $this->count++;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove(AssetInterface $asset) : bool {

        $index = $this->indexOf($asset);
        if (-1 === $index) {
            return false;
        }

        $this->innerContainer[$index] = $this->innerContainer[$this->count - 1];
        unset($this->innerContainer[$this->count - 1]);
        $this->count--;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function removeAll() {
        $this->count = 0;
        unset($this->innerContainer);
        $this->innerContainer = array();
    }

    /**
     * @inheritDoc
     */
    public function find(\Closure $closure) {
        for ($i = $this->count; $i-->0;) {
            if ($closure($this->innerContainer[$i])) {
                return $this->innerContainer[$i];
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getIterator() {
        $iterator = new ArrayIterator($this->innerContainer);
        return $iterator;
    }

    /**
     * @inheritDoc
     */
    public function count() {
        return $this->count;
    }

    /**
     * @inheritDoc
     */
    public function exists(AssetInterface $asset) : bool {

        $result = -1 !== $this->indexOf($asset);
        return $result;
    }

    /**
     * @param AssetInterface $asset
     * @return int
     */
    private function indexOf(AssetInterface $asset) : int {

        for ($i = $this->count; $i-->0;) {
            /** @var Asset $curr */
            $curr = $this->innerContainer[$i];

            if ($curr === $asset) {
                return $i;
            }

            if ($curr->getType() === $asset->getType() &&
                $curr->getName() === $asset->getName()) {
                return $i;
            }

        }
        return -1;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return $this->innerContainer;
    }

    /**
     * @inheritDoc
     */
    public function getBaseUrl() : string {
        return $this->baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function setBaseUrl(string $baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function getBasePath() {
        return $this->basePath;
    }

    /**
     * @inheritDoc
     */
    public function setBasePath(string $basePath = null) {
        $this->basePath = $basePath;
    }

    /**
     * @inheritDoc
     */
    public function getPrintPattern() {
        return $this->printPattern;
    }

    /**
     * @inheritDoc
     */
    public function getFileRegex() {
        return $this->fileRegex;
    }

    /**
     * @inheritDoc
     */
    public function getType() : string {
        return $this->type;
    }

}
