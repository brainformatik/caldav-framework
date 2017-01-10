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
 * Class Contact
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.2
 *
 * @package Brainformatik\CalDAV\Type
 */
class Contact implements CalAddressTypeInterface {

    /**
     * String containing name and address data
     *
     * @var string
     */
    protected $contactData;

    /**
     * Format of tag is defined in RFC5646
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.10
     */
    protected $language;

    /**
     * To specify an alternate text representation for the property value
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.1
     */
    protected $alternateRepresentation;

    /**
     * @param string $contactData - Contact details as a comma separated string (John Doe, Main Street 1, Virginia)
     */
    public function __construct($contactData) {
        $this->contactData = $contactData;
    }

    /**
     * Returns the contact details
     *
     * @return string
     */
    public function getAddress() {
        return $this->contactData;
    }

    /**
     * Returns a list with set parameters
     *
     * @return array
     */
    public function getParameters() {
        $parameters = [];

        if (!empty($this->language)) {
            $parameters['LANGUAGE'] = $this->language;
        }

        if (!empty($this->alternateRepresentation)) {
            $parameters['ALTREP'] = $this->alternateRepresentation;
        }

        return $parameters;
    }

    /**
     * Sets the language of attendee
     *
     * @param string $language - Tag format defined in RFC5646
     *
     * @return Contact
     */
    public function setLanguage($language) {

        if (!empty($language)) {
            $this->language = $language;
        }

        return $this;
    }

    /**
     * Set alternate representation (URI)
     *
     * @param string $altRep
     *
     * @return Contact
     */
    public function setAlternateRepresentation($altRep) {

        if (!empty($altRep)) {
            $this->alternateRepresentation = $altRep;
        }

        return $this;
    }
}