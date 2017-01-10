<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Connection;

use Sabre\DAV\Client as SabreDAVClient;

class Client extends SabreDAVClient {

    /**
     * Check if the authentication data specified in the constructor is correct
     *
     * @return bool
     */
    public function isValidConnection() {
        return in_array('calendar-access', $this->options());
    }

    /**
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUri;
    }
}