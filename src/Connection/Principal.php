<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Connection;

use Brainformatik\CalDAV\Entity\Calendar;

class Principal {

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param Client $client
     * @param string $url
     */
    public function __construct(Client $client, $url) {
        $this->client = $client;
        $this->setUrl($url);
    }

    /**
     * @param string $url
     */
    public function setUrl($url) {
        if (!is_string($url)) {
            throw new \InvalidArgumentException('You can only pass strings to url!');
        }

        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return Calendar[]
     */
    public function getCalendars() {
        $calendars = [];

        $homeSet = $this->getHomeSetUrl();

        $calendarResult = $this->client->propFind($homeSet, [
            '{DAV:}displayname'
        ], 1);

        foreach ($calendarResult as $url => $properties) {

            if (!empty($properties['{DAV:}displayname'])) {

                $tmpCalendar = new Calendar($this->client, $url);
                $tmpCalendar->setDisplayName($properties['{DAV:}displayname']);

                $calendars[] = $tmpCalendar;
            }
        }

        return $calendars;
    }

    /**
     * Returns the calendar-home-set that is used to determine all calendars
     *
     * @return string
     */
    public function getHomeSetUrl() {

        $result = $this->client->propFind($this->url, [
            '{urn:ietf:params:xml:ns:caldav}calendar-home-set'
        ]);

        $homeSet = $result['{urn:ietf:params:xml:ns:caldav}calendar-home-set'][0]['value'];

        if (empty($homeSet)) {
            throw new \RuntimeException('Unable to determine a valid calendar-home-set URL!');
        }

        return $homeSet;
    }
}