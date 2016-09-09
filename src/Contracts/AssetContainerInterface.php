<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetContainerInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-18 - kl 10:27
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

use Countable;
use IteratorAggregate;

/**
 * @internal
 */
interface AssetContainerInterface extends Countable, IteratorAggregate {

    /**
     * Add an asset.
     *
     * @param AssetInterface $asset
     * @return bool
     */
    public function add(AssetInterface $asset) : bool;

    /**
     * Remove an asset by name or path.
     *
     * @param AssetInterface|string $asset
     * @return bool
     */
    public function remove(AssetInterface $asset) : bool;

    /**
     * Remove all assets from container.
     *
     * @return void
     */
    public function removeAll();

    /**
     * Find the first asset which fulfills the supplied closure.
     *
     * @param \Closure $closure Will be passed the asset to test and should return true if found.
     * @return AssetInterface|null
     */
    public function find(\Closure $closure);

    /**
     * Check if given asset exists in the container.
     *
     * @param AssetInterface $asset
     * @return bool
     */
    public function exists(AssetInterface $asset) : bool;
}
