<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Type;

interface StringTypeInterface {

    /**
     * Returns the string representation of the object
     *
     * @return string - representation of the type according to RFC5545
     *
     * @throws \UnexpectedValueException - In case an attribute has a wrong value
     */
    public function toString();
}