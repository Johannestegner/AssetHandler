<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  ContainerDataInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-09-09 - kl 15:55
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

/**
 * @internal
 */
interface ContainerDataInterface {

    /**
     * Get the base URL of all assets in the container.
     *
     * @internal Should only be used by the Asset when fetching full URL.
     * @return string
     */
    public function getBaseUrl() : string;

    /**
     * Set the base URL of all assets in the container.
     *
     * @internal Should only be used by the asset handler.
     * @param string $baseUrl
     * @return void
     */
    public function setBaseUrl(string $baseUrl);

    /**
     * Get the base path of all assets in the container.
     *
     * @return string|null
     */
    public function getBasePath();

    /**
     * Set the base path of all assets in the container.
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
     * uses when determine where to put assets by using file type.
     *
     * @return string|null
     */
    public function getFileRegex();

    /**
     * Get the type name of the container.
     *
     * @return string
     */
    public function getType() : string;
}
