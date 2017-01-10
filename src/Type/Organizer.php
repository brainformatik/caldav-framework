<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Type;

use Brainformatik\CalDAV\Enum\CalendarUserType;
use Brainformatik\CalDAV\Enum\ParticipantRole;
use Brainformatik\CalDAV\Enum\ParticipantStatus;
use Brainformatik\CalDAV\Enum\ParticipantStatusTodo;

/**
 * Class Organizer
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.3
 *
 * @package Brainformatik\CalDAV\Type
 */
class Organizer implements CalAddressTypeInterface {

    /**
     * @var string
     */
    protected $mailAddress;

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.2
     */
    protected $name;

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.18
     */
    protected $sentBy;

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.6
     */
    protected $directory;

    /**
     * Format of tag is defined in RFC5646
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.10
     */
    protected $language;

    /**
     * @param string $mailAddress
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($mailAddress) {
        $this->mailAddress = $mailAddress;
    }

    /**
     * Returns the mail address of the attendee prefixed with "mailto:"
     *
     * @return string
     */
    public function getAddress() {
        return 'mailto:' . $this->mailAddress;
    }

    /**
     * Returns a list with set parameters
     *
     * @return array
     */
    public function getParameters() {
        $parameters = [];

        if (!empty($this->name)) {
            $parameters['CN'] = $this->name;
        }

        if (!empty($this->sentBy)) {
            $parameters['SENT-BY'] = $this->sentBy;
        }

        if (!empty($this->directory)) {
            $parameters['DIR'] = $this->directory;
        }

        if (!empty($this->language)) {
            $parameters['LANGUAGE'] = $this->language;
        }

        return $parameters;
    }

    /**
     * Sets the name of the attendee
     *
     * @param string $name
     *
     * @return Organizer
     */
    public function setName($name) {

        if (!empty($name)) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * Sets value for "sent by" parameter
     *
     * @param string $sentBy - Email address
     *
     * @return Organizer
     */
    public function setSentBy($sentBy) {

        if (!empty($sentBy)) {
            $this->sentBy = 'mailto:' . $sentBy;
        }

        return $this;
    }

    /**
     * Sets value for "directory" parameter
     *
     * @param string $directory - URI like CID, DATA, FILE, FTP, HTTP, HTTPS, LDAP or MID schemes
     *
     * @return Organizer
     */
    public function setDirectory($directory) {

        if (!empty($directory)) {
            $this->directory = $directory;
        }

        return $this;
    }

    /**
     * Sets the language of attendee
     *
     * @param string $language - Tag format defined in RFC5646
     *
     * @return Organizer
     */
    public function setLanguage($language) {

        if (!empty($language)) {
            $this->language = $language;
        }

        return $this;
    }
}