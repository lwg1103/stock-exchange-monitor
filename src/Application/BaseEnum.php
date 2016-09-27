<?php

namespace Application;

use ReflectionClass;

/**
 * Class BaseEnum
 *
 * source - http://stackoverflow.com/questions/254514/php-and-enumerations
 *
 * @package AppBundle
 */
abstract class BaseEnum
{
    private static $constCacheArray = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function isValid($value)
    {
        $values = array_values(self::getConstants());

        return in_array($value, $values);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function toString($value)
    {
        $values = array_values(self::getConstants());
        $keys = array_keys(self::getConstants());

        $index = array_search($value, $values);

        return strtolower($keys[$index]);
    }

    /**
     * @return mixed
     */
    public static function getValidKeys()
    {
        return implode(', ', array_keys(self::getConstants()));
    }

    private static function getConstants()
    {
        if (self::$constCacheArray == null) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }
}