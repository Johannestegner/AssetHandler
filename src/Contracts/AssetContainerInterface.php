<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetContainerInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-18 - kl 10:27
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

/**
 * @internal
 */
interface AssetContainerInterface {

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

    /**
     * Get the base path of all assets in the container.
     *
     * @internal Should only be used by the Asset when fetching full URL.
     * @return string
     */
    public function getBaseUrl() : string;

    /**
     * Set the base path of all assets in the container.
     *
     * @internal Should only be used by the asset handler.
     * @param string $baseUrl
     * @return void
     */
    public function setBaseUrl(string $baseUrl);

}
