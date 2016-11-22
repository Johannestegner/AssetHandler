<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  ContainerDataInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-09-09 - kl 15:55
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\AssetHandler\Internal\Contracts;

/**
 * @internal
 */
interface ContainerDataInterface {

    /**
     * Get the base URL used by assets in the container.
     *
     * @return string
     */
    public function getBaseUrl() : string;

    /**
     * Set the base URL to be used by all assets in the container.
     *
     * @param string $baseUrl
     * @return void
     */
    public function setBaseUrl(string $baseUrl);

    /**
     * Get the base path used by all assets in the container.
     *
     * @return string|null
     */
    public function getBasePath();

    /**
     * Set the base path to be used by assets in the container.
     *
     * @param string|null $basePath
     */
    public function setBasePath(string $basePath = null);

    /**
     * Get the pattern which the assets are formatted with when printed.
     *
     * @return string|null
     */
    public function getPrintPattern();

    /**
     * Get regular expression used to determine file type - of assets - that a given container
     * uses when determine where to put assets by using file type (when leaving out the container name).
     *
     * @return string|null
     */
    public function getFileRegex();

    /**
     * Get the type name of the container.
     *
     * @alias getName
     * @see ContainerDataInterface::getName()
     * @return string
     */
    public function getType() : string;

    /**
     * Get tye type name of the container.
     *
     * @alias getType
     * @see ContainerDataInterface::getType()
     * @return string
     */
    public function getName() : string;

    /**
     * Check if the container is versioning its assets by appending the assets last change timestamp to its
     * filename when printing it.
     *
     * @return bool
     */
    public function isUsingVersioning() : bool;

    /**
     * Change state on versioning.
     * If true, the assets in the container will be versioned by appending the asset last change timestamp to the
     * filename when printing it.
     *
     * Example:
     * <code>test.js</code> will become <code>test.js?1234567891</code>
     *
     * @param bool $state
     * @return void
     */
    public function setIsUsingVersioning(bool $state);
}
