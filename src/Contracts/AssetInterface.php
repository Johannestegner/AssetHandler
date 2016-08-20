<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetInterface.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 9:03
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Contracts;

interface AssetInterface {

    /**
     * Get type of asset.
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Get path to the asset (excludes name).
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
     * Full path of the asset, including name.
     *
     * @return string
     */
    public function getFullPath() : string;
}
