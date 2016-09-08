<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetTypes.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 16:34
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler\Types;

use ReflectionClass;

final class AssetTypes {

    private function __construct() { }

    const SCRIPT      = "scripts";
    const STYLE_SHEET = "style sheets";
    const IMAGE       = "images";
    const ANY         = "any";

    private static $cache          = array();
    private static $cacheGenerated = false;

    private static function generateCache() {
        self::$cache['keys']   = array();
        self::$cache['values'] = array();
        $self                  = new ReflectionClass(__CLASS__);
        $constants             = $self->getConstants();

        foreach ($constants as $name => $value) {
            self::$cache['keys'][]   = $name;
            self::$cache['values'][] = $value;
        }
    }

    /**
     * @return string[]
     */
    public static function getNames() {
        if (!self::$cacheGenerated) {
            self::generateCache();
        }

        return self::$cache['keys'];
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function hasType(string $type) {
        $values = self::getTypes();
        $result = in_array($type, $values);

        return $result;
    }

    /**
     * @return string[]
     */
    public static function getTypes() {
        if (!self::$cacheGenerated) {
            self::generateCache();
        }

        return self::$cache['values'];
    }
}
