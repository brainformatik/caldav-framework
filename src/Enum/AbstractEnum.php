<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class AbstractEnum
 *
 * Base class for enums
 *
 * @package Brainformatik\CalDAV\Enum
 */
abstract class AbstractEnum {

    /**
     * @var array
     */
    protected static $constantsCache = [];

    /**
     * Check if value is valid for enum
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function has($value) {
        return in_array($value, self::toArray(), true);
    }

    /**
     * @return array
     */
    public static function toArray() {
        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, self::$constantsCache)) {
            $reflection = new \ReflectionClass($calledClass);
            self::$constantsCache[$calledClass] = $reflection->getConstants();
        }

        return self::$constantsCache[$calledClass];
    }
}