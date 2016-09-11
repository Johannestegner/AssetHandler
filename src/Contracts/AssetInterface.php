<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 9:03
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

/**
 * @internal
 */
interface AssetInterface {

    /**
     * Get type of asset.
     * The type will be the same as the container type name.
     *
     * @see ContainerDataInterface::getType()
     * @see ContainerDataInterface::getName()
     * @return string
     */
    public function getType() : string;

    /**
     * Get path to the asset.
     *
     * @return string
     */
    public function getPath() : string;

    /**
     * Get name of asset.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Fetch the container which the asset dwells in.
     *
     * @return AssetContainerInterface|null
     */
    public function getContainer();

    /**
     * Set container for the given asset.
     *
     * @param AssetContainerInterface $container
     * @return void
     */
    public function setContainer(AssetContainerInterface $container);
}
