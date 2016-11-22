<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  FormattableException.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-29 - kl 17:31
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\AssetHandler\Internal\Exceptions;

use Exception;

class FormattableException extends Exception {
    /**
     * @param string $format
     * @param array  ...$args
     */
    public function __construct(string $format, ... $args) {
        parent::__construct(sprintf($format, ... $args));
    }
}
