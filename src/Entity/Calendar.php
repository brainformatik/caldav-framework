<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Entity;

use Brainformatik\CalDAV\Connection\Client;
use Sabre\VObject\Component\VCalendar;

class Calendar {

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var VCalendar
     */
    protected $calendar;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $displayname;

    /**
     * @param Client $client
     * @param string $url
     */
    public function __construct(Client $client, $url) {
        $this->client = $client;
        $this->calendar = new VCalendar();
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
     * @param string $name
     */
    public function setDisplayName($name) {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('You can only pass strings to name!');
        }

        $this->displayname = $name;
    }

    /**
     * @return string
     */
    public function getDisplayName() {
        return $this->displayname;
    }

    /**
     * @return Event
     */
    public function addEvent() {
        return new Event($this->calendar);
    }

    /**
     * @return Todo
     */
    public function addTodo() {
        return new Todo($this->calendar);
    }

    /**
     * @param integer $options
     *
     * @return array
     */
    public function validate($options) {
        return $this->calendar->validate($options);
    }

    /**
     * @return string
     */
    public function serialize() {
        return $this->calendar->serialize();
    }

    /**
     * @param string $fileName
     * @param string $eTag
     *
     * @return array
     */
    public function save($fileName, $eTag = '') {
        $headers = [];

        if (!empty($eTag)) {
            $headers['If-Match'] = $eTag;
        }

        return $this->client->request('PUT', $this->url . $fileName, $this->serialize(), $headers);
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    public function delete($fileName) {
        return $this->client->request('DELETE', $this->url . $fileName);
    }
}