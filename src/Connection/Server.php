<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Connection;

class Server {

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Returns all accessible principals on the data provided in the Client object.
     *
     * @return Principal[]
     */
    public function getPrincipals() {
        $principals = [];

        $response = $this->findPrincipals();

        foreach ($response['{DAV:}current-user-principal'] as $principalData) {
            $principals[] = new Principal($this->client, $principalData['value']);
        }

        return $principals;
    }

    /**
     * @return bool
     */
    public function isValid() {
        try {
            $response = $this->findPrincipals();

            return !empty($response['{DAV:}current-user-principal']);
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * @return array
     */
    protected function findPrincipals() {

        return $this->client->propFind($this->client->getBaseUrl(), [
            '{DAV:}current-user-principal'
        ]);
    }
}