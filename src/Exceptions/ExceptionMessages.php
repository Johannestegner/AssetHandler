<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  ExceptionMessages.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-29 - kl 10:58
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Exceptions;

/**
 * @internal
 */
final class ExceptionMessages {
    private function __construct() { }

    const CONTAINER_NOT_EXIST          = 'Container named "%s" does not exist.';
    const CONTAINER_NOT_DETERMINABLE   = 'Could not determine container from the asset path (%s).';
    const CONTAINER_NOT_UNIQUE         = 'Container named "%s" already exist.';
    const INVALID_PATH                 = 'The path "%s" is invalid.';
    const INVALID_ASSET_PATH           = 'Asset path invalid for asset named "%s" (%s).';
    const ASSET_NOT_CONTAINER_UNIQUE   = 'An asset with the name "%s" already exists in the container (%s).';
    const ASSET_NOT_HANDLER_UNIQUE     = 'Asset name "%s" exists in multiple containers. Container param is required.';
    const ASSET_NOT_EXIST_IN_CONTAINER = 'Could not locate asset "%s" in the container "%s".';
    const ASSET_NOT_EXIST              = 'Could not locate asset "%s".';
    const PRINT_PATTERN_MISSING        = 'Found no print pattern for container "%s".';

}
