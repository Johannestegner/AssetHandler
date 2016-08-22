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
use Traversable;

class AssetContainer implements AssetContainerInterface, Countable, IteratorAggregate {

    private $innerContainer = array();
    private $count          = 0;

    /**
     * Add an asset.
     *
     * @param AssetInterface $asset
     * @return bool
     */
    public function add(AssetInterface $asset) : bool {
        // Check if asset already in the container.

        if ($this->exists($asset, true)) {
            return false;
        }

        $this->innerContainer[$this->count] = $asset;
        $this->count++;
        return true;
    }

    /**
     * Remove an asset.
     *
     * @param AssetInterface $asset
     * @return bool
     */
    public function remove(AssetInterface $asset) : bool {

        $index = $this->indexOf($asset, true);
        if (-1 === $index) {
            return false;
        }

        $this->innerContainer[$index] = $this->innerContainer[$this->count - 1];
        unset($this->innerContainer[$this->count - 1]);
        $this->count--;

        return true;
    }

    /**
     * Remove all assets from container.
     *
     * @return void
     */
    public function removeAll() {
        $this->count = 0;
        unset($this->innerContainer);
        $this->innerContainer = array();
    }

    /**
     * Find the first asset which fulfills the supplied closure.
     *
     * @param \Closure $closure Will be passed the asset to test and should return true if found.
     * @return AssetInterface|null
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
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() {
        $iterator = new ArrayIterator($this->innerContainer);
        return $iterator;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count() {
        return $this->count;
    }

    /**
     * Check if given asset exists in the container.
     *
     * @param AssetInterface $asset
     * @param bool $strict
     * @return bool
     */
    public function exists(AssetInterface $asset, bool $strict = false) : bool {

        $result = -1 !== $this->indexOf($asset, $strict);
        return $result;
    }

    /**
     * @param AssetInterface $asset
     * @param bool           $strict
     * @return int
     */
    private function indexOf(AssetInterface $asset, bool $strict) : int {

        for ($i = $this->count; $i-->0;) {
            /** @var Asset $curr */
            $curr = $this->innerContainer[$i];

            if ($curr === $asset) {
                return $i;
            }

            if (true == $strict &&
                $curr->getFullPath() === $asset->getFullPath() &&
                $curr->getType() === $asset->getType()) {

                return $i;
            }

        }
        return -1;
    }
}
