<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Type;

interface CalAddressTypeInterface {

    /**
     * Returns the mail address of the attendee prefixed with "mailto:"
     *
     * @return string
     */
    public function getAddress();

    /**
     * Returns a list with set parameters
     *
     * @return array
     */
    public function getParameters();
}