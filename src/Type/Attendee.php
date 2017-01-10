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
 * Class Attendee
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.1
 *
 * @package Brainformatik\CalDAV\Type
 */
class Attendee implements CalAddressTypeInterface {

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
     * Default: REQ-PARTICIPANT
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.16
     */
    protected $role;

    /**
     * Default: NEEDS-ACTION
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.12
     */
    protected $status;

    /**
     * Internal type for checkings
     *
     * @var string
     */
    protected $entityType;

    /**
     * Default: FALSE
     *
     * @var boolean
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.17
     */
    protected $rsvp;

    /**
     * Default: INDIVIDUAL
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.3
     */
    protected $userType;

    /**
     * @var array
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.11
     */
    protected $groupMembers = [];

    /**
     * @var array
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.5
     */
    protected $delegatedTo = [];

    /**
     * @var array
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.2.4
     */
    protected $delegatedFrom = [];

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
     * @param string $targetEntityType - Determines if target entity for the attendee is 'Event' or 'Todo'
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($mailAddress, $targetEntityType = 'Event') {

        if (empty($mailAddress)) {
            throw new \InvalidArgumentException('Mail address cannot be empty!');
        }

        if ('Event' !== $targetEntityType && 'Todo' !== $targetEntityType) {
            throw new \InvalidArgumentException('Target entity type must be "Event" or "Todo"!');
        }

        $this->mailAddress = $mailAddress;
        $this->entityType = $targetEntityType;
    }

    /**
     * Returns the entity type
     *
     * @return string
     */
    public function getEntityType() {
        return $this->entityType;
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

        if (!empty($this->role)) {
            $parameters['ROLE'] = $this->role;
        }

        if (!empty($this->status)) {
            $parameters['PARTSTAT'] = $this->status;
        }

        if (true === $this->rsvp) {
            $parameters['RSVP'] = 'TRUE';
        } else if (false === $this->rsvp) {
            $parameters['RSVP'] = 'FALSE';
        }

        if (!empty($this->userType)) {
            $parameters['CUTYPE'] = $this->userType;
        }

        if (count($this->groupMembers)) {
            $parameters['MEMBER'] = $this->groupMembers;
        }

        if (count($this->delegatedTo)) {
            $parameters['DELEGATED-TO'] = $this->delegatedTo;
        }

        if (count($this->delegatedFrom)) {
            $parameters['DELEGATED-FROM'] = $this->delegatedFrom;
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
     * @return Attendee
     */
    public function setName($name) {

        if (!empty($name)) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * Value must be out of enum ParticipantRole
     *
     * @param string $role
     *
     * @return Attendee
     *
     * @throws \InvalidArgumentException
     */
    public function setRole($role) {

        if (!ParticipantRole::has($role)) {
            throw new \InvalidArgumentException('Role not in enum ParticipantRole!');
        }

        $this->role = $role;

        return $this;
    }

    /**
     * Value must be out of enum ParticipantStatus for events and ParticipantStatusTodo for todos
     *
     * @param string $status
     *
     * @return Attendee
     *
     * @throws \InvalidArgumentException
     */
    public function setParticipantStatus($status) {

        if ('Event' === $this->entityType && !ParticipantStatus::has($status)) {
            throw new \InvalidArgumentException('Status not in enum ParticipantStatus!');
        }

        if ('Todo' === $this->entityType && !ParticipantStatusTodo::has($status)) {
            throw new \InvalidArgumentException('Status not in enum ParticipantStatusTodo!');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Value must be out of enum CalendarUserType
     *
     * @param string $type
     *
     * @return Attendee
     *
     * @throws \InvalidArgumentException
     */
    public function setUserType($type) {

        if (!CalendarUserType::has($type)) {
            throw new \InvalidArgumentException('Value not in enum CalendarUserType!');
        }

        $this->userType = $type;

        return $this;
    }

    /**
     * Adds new mail address to the list of groups
     *
     * @param string $mailAddress
     *
     * @return Attendee
     */
    public function addGroupMember($mailAddress) {

        if (!empty($mailAddress)) {
            $this->groupMembers[] = 'mailto:' . $mailAddress;
        }

        return $this;
    }

    /**
     * Adds new mail address to the list of delegated to users
     *
     * @param string $mailAddress
     *
     * @return Attendee
     */
    public function addDelegatedTo($mailAddress) {

        if (!empty($mailAddress)) {
            $this->delegatedTo[] = 'mailto:' . $mailAddress;
        }

        return $this;
    }

    /**
     * Adds new mail address to the list of delegated from users
     *
     * @param string $mailAddress
     *
     * @return Attendee
     */
    public function addDelegatedFrom($mailAddress) {

        if (!empty($mailAddress)) {
            $this->delegatedFrom[] = 'mailto:' . $mailAddress;
        }

        return $this;
    }

    /**
     * Sets value for "sent by" parameter
     *
     * @param string $sentBy - Email address
     *
     * @return Attendee
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
     * @return Attendee
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
     * @return Attendee
     */
    public function setLanguage($language) {

        if (!empty($language)) {
            $this->language = $language;
        }

        return $this;
    }
}