<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Entity;

use Brainformatik\CalDAV\Helper\TimeZone;
use Brainformatik\CalDAV\Type\Attendee;
use Brainformatik\CalDAV\Type\Contact;
use Brainformatik\CalDAV\Type\Duration;
use Brainformatik\CalDAV\Type\Organizer;
use Brainformatik\CalDAV\Type\Period;
use Brainformatik\CalDAV\Type\RecurrenceRule;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Component\VEvent;
use Sabre\VObject\Component\VTodo;

abstract class AbstractEntity {

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var VEvent|VTodo
     */
    protected $instance;

    /**
     * @var VCalendar
     */
    protected $calendar;

    /**
     * This property defines a short summary or subject
     *
     * @param string $uid
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.7
     */
    public function setUid($uid, array $parameters = null) {

        if (!empty($uid)) {
            $uidInstance = $this->instance->select('UID');

            // uid property already exist
            if (count($uidInstance)) {
                $uidInstance[0]->setValue($uid);
            } else { // create new property if not existing
                $this->instance->add($this->calendar->createProperty('UID', $uid, $parameters));
            }
        }
    }

    /**
     * This property defines a short summary or subject
     *
     * @param string $summary
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.12
     */
    public function setSummary($summary, array $parameters = null) {

        if (!empty($summary)) {
            $this->instance->add($this->calendar->createProperty('SUMMARY', $summary, $parameters));
        }
    }

    /**
     * This property provides a more complete description
     *
     * @param string $description
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.5
     */
    public function setDescription($description, array $parameters = null) {

        if (!empty($description)) {
            $this->instance->add($this->calendar->createProperty('DESCRIPTION', $description, $parameters));
        }
    }

    /**
     * This property specifies non-processing information intended to provide a comment to the calendar user
     *
     * @param string $comment
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.4
     */
    public function setComment($comment, array $parameters = null) {

        if (!empty($comment)) {
            $this->instance->add($this->calendar->createProperty('COMMENT', $comment, $parameters));
        }
    }

    /**
     * This property provides the capability to associate a document object
     *
     * Currently only a URI is supported!
     *
     * @param string $attachmentPath
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.1
     */
    public function addAttachment($attachmentPath, array $parameters = null) {

        if (!empty($attachmentPath)) {
            $this->instance->add($this->calendar->createProperty('ATTACH', $attachmentPath, $parameters));
        }
    }

    /**
     * This property is used to specify categories or subtypes
     *
     * @param array $categories
     * @param array $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.2
     */
    public function addCategories(array $categories, array $parameters = null) {

        if (count($categories)) {
            $this->instance->add($this->calendar->createProperty('CATEGORIES', $categories, $parameters));
        }
    }

    /**
     * This property defines the access classification
     *
     * Recommended values: "PUBLIC", "PRIVATE" or "CONFIDENTIAL"
     *
     * @param string $class
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.3
     */
    public function setClass($class, array $parameters = null) {

        if (!empty($class)) {
            $this->instance->add($this->calendar->createProperty('CLASS', $class, $parameters));
        }
    }

    /**
     * This property specifies information related to the global position
     *
     * @param array $latLong - Latitude and longitude
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.6
     */
    public function setGeo(array $latLong, array $parameters = null) {

        if (2 !== count($latLong) || !is_numeric($latLong[0]) || !is_numeric($latLong[1])) {
            throw new \InvalidArgumentException('The array should contain two numbers!');
        }

        $this->instance->add($this->calendar->createProperty('GEO', $latLong, $parameters));
    }

    /**
     * This property defines the intended venue for the activity
     *
     * @param string $location
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.7
     */
    public function setLocation($location, array $parameters = null) {

        if (!empty($location)) {
            $this->instance->add($this->calendar->createProperty('LOCATION', $location, $parameters));
        }
    }

    /**
     * This property defines the relative priority
     *
     * 0 = undefined, 1 = highest, 9 = lowest
     *
     * @param int   $priority
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.9
     */
    public function setPriority($priority, array $parameters = null) {

        if (!is_int($priority)) {
            throw new \InvalidArgumentException('Priority should be an integer value!');
        }

        if (0 > $priority || $priority > 9) {
            throw new \OutOfRangeException('Priority should be between 0 and 9!');
        }

        $this->instance->add($this->calendar->createProperty('PRIORITY', $priority, $parameters));
    }

    /**
     * This property defines the equipment or resources anticipated for an activity
     *
     * @param array $resources
     * @param array $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.10
     */
    public function addResources(array $resources, array $parameters = null) {

        if (count($resources)) {
            $this->instance->add($this->calendar->createProperty('RESOURCES', $resources, $parameters));
        }
    }

    /**
     * This property defines the overall status or confirmation
     *
     * @param string $status     - Value of enum EventStatus or TodoStatus depending on type of entity
     * @param array  $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.11
     */
    public function setStatus($status, array $parameters = null) {

        if (!$this->isStatusValid($status)) {
            throw new \InvalidArgumentException('This status is not allowed for current entity!');
        }

        $this->instance->add($this->calendar->createProperty('STATUS', $status, $parameters));
    }

    /**
     * This property specifies when the calendar component begins
     *
     * Time zone of the given parameter should be explicitly set. Otherwise time zone of server will be used.
     *
     * Note: If you only want to use the date part you can set parameter VALUE => DATE
     *
     * @param \DateTimeInterface $start
     * @param bool               $isFloating
     * @param array              $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.4
     */
    public function setDateStart(\DateTimeInterface $start, $isFloating = false, array $parameters = null) {
        $this->instance->add($this->createDateProperty('DTSTART', $start, $isFloating, $parameters));
    }

    /**
     * This property specifies a duration of time
     *
     * @param Duration $duration
     * @param array    $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.5
     */
    public function setDuration(Duration $duration, array $parameters = null) {
        $this->instance->add($this->calendar->createProperty('DURATION', $duration->toString(), $parameters));
    }

    /**
     * This property is used to represent a relationship or reference between one calendar component and another
     *
     * Note: To specify the type of relation you can use parameter RELTYPE (default: "PARENT", "SIBLING", "CHILD")
     *
     * @param string $relatedTo
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.5
     */
    public function setRelatedTo($relatedTo, array $parameters = null) {

        if (!empty($relatedTo)) {
            $this->instance->add($this->calendar->createProperty('RELATED-TO', $relatedTo, $parameters));
        }
    }

    /**
     * This property defines a Uniform Resource Locator (URL) associated with the iCalendar object
     *
     * @param string $url
     * @param array  $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.6
     */
    public function setUrl($url, array $parameters = null) {

        if (!empty($url)) {
            $this->instance->add($this->calendar->createProperty('URL', $url, $parameters));
        }
    }

    /**
     * This property defines an "Attendee"
     *
     * @param Attendee $attendee
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.1
     */
    public function addAttendee(Attendee $attendee) {

        if ($this->type !== $attendee->getEntityType()) {
            throw new \InvalidArgumentException('Type of entity and target entity type of attendee must match!');
        }

        $this->instance->add($this->calendar->createProperty('ATTENDEE', $attendee->getAddress(), $attendee->getParameters()));
    }

    /**
     * This property is used to represent contact information or alternately a reference to contact information
     *
     * @param Contact $contact
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.2
     */
    public function setContact(Contact $contact) {
        $this->instance->add($this->calendar->createProperty('CONTACT', $contact->getAddress(), $contact->getParameters()));
    }

    /**
     * This property is used to represent contact information or alternately a reference to contact information
     *
     * @param Organizer $organizer
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.3
     */
    public function setOrganizer(Organizer $organizer) {
        $this->instance->add($this->calendar->createProperty('ORGANIZER', $organizer->getAddress(), $organizer->getParameters()));
    }

    /**
     * This property specifies the date and time that the calendar information was created by the calendar user agent
     *
     * This value has to use time zone UTC
     *
     * @param \DateTimeInterface $created
     * @param array              $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.7.1
     */
    public function setDateCreated(\DateTimeInterface $created, array $parameters = null) {
        $timeZone = $created->getTimezone();

        if ('UTC' !== $timeZone->getName()) {
            throw new \InvalidArgumentException('The value must use UTC as time zone!');
        }

        $this->instance->add($this->calendar->createProperty('CREATED', $created, $parameters));
    }

    /**
     * This property specifies the date and time that the information associated with the calendar component was last revised
     *
     * This value has to use time zone UTC
     *
     * @param \DateTimeInterface $lastModified
     * @param array              $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.7.3
     */
    public function setDateLastModified(\DateTimeInterface $lastModified, array $parameters = null) {
        $timeZone = $lastModified->getTimezone();

        if ('UTC' !== $timeZone->getName()) {
            throw new \InvalidArgumentException('The value must use UTC as time zone!');
        }

        $this->instance->add($this->calendar->createProperty('LAST-MODIFIED', $lastModified, $parameters));
    }

    /**
     * This property defines the revision sequence number of the calendar component within a sequence of revisions
     *
     * @param int   $sequence
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.7.4
     */
    public function setSequence($sequence, array $parameters = null) {

        if (!is_int($sequence)) {
            throw new \InvalidArgumentException('Sequence should be an integer value!');
        }

        $this->instance->add($this->calendar->createProperty('SEQUENCE', $sequence, $parameters));
    }

    /**
     * This property is used in conjunction with the "UID" and "SEQUENCE" properties to identify a specific instance of a recurring "VEVENT" or "VTODO"
     *
     * @param string     $id
     * @param array|null $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.4
     */
    public function setRecurrenceId($id, array $parameters = null) {

        if (!empty($id)) {
            $this->instance->add($this->calendar->createProperty('RECURRENCE-ID', $id, $parameters));
        }
    }

    /**
     * This property defines the list of DATE-TIME exceptions for recurring events or to-dos
     *
     * Note: Every date in the $dates array will be converted to the time zone of the first date
     *
     * @param \DateTimeInterface[] $dates
     * @param array|null           $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.5.1
     */
    public function addExceptionDates(array $dates, array $parameters = null) {

        if (count($dates)) {
            $this->instance->add($this->calendar->createProperty('EXDATE', $dates, $parameters));
        }
    }

    /**
     * This property defines the list of DATE-TIME values for recurring events or to-dos
     *
     * Note: Every date in the $dates array will be converted to the time zone of the first date
     *
     * @param \DateTimeInterface[] $dates
     * @param array|null           $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.5.2
     */
    public function addRecurrenceDates(array $dates, array $parameters = null) {

        if (isset($parameters['VALUE']) && 'PERIOD' === mb_strtoupper($parameters['VALUE'])) {
            throw new \InvalidArgumentException('You have to use addRecurrencePeriods to set periods!');
        }

        if (count($dates)) {
            $this->checkTimeZone($dates[0]);
            $this->instance->add($this->calendar->createProperty('RDATE', $dates, $parameters));
        }
    }

    /**
     * This property defines the list of PERIOD values for recurring events or to-dos
     *
     * @param Period[]   $periods
     * @param array|null $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.5.2
     */
    public function addRecurrencePeriods(array $periods, array $parameters = null) {

        $periods = array_map(function($period) {

            if (!($period instanceof Period)) {
                throw new \InvalidArgumentException('All values of periods must be instances of Period!');
            }

            // convert periods to string representation because vobjects don't know about our own Period class
            return $period->toString();
        }, $periods);

        if (count($periods)) {
            $parameters['VALUE'] = 'PERIOD';
            $this->instance->add($this->calendar->createProperty('RDATE', $periods, $parameters));
        }
    }

    /**
     * This property defines a rule or repeating pattern for recurring events or to-dos
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.5.3
     *
     * @param RecurrenceRule $rule
     */
    public function setRecurrenceRule(RecurrenceRule $rule) {
        $this->instance->add($this->calendar->createProperty('RRULE', $rule->toString()));
    }
    
    /**
     * Add alarm component to current entity
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.6.6
     *
     * @return Alarm
     */
    public function addAlarm() {
        return new Alarm($this->calendar, $this->instance);
    }

    /**
     * Creates a new date time property including check for floating date time
     *
     * @param string             $name
     * @param \DateTimeInterface $date
     * @param bool               $isFloating
     * @param array              $parameters
     *
     * @return \Sabre\VObject\Property
     */
    protected function createDateProperty($name, \DateTimeInterface $date, $isFloating, array $parameters = null) {

        if ($isFloating) {
            $property = $this->calendar->createProperty($name, '', $parameters);
            $property->setDateTime($date, $isFloating);
        } else {
            $this->checkTimeZone($date);
            $property = $this->calendar->createProperty($name, $date, $parameters);
        }

        return $property;
    }

    /**
     * Checks if a time zone must be added to the calendar and adds it if true
     *
     * @param \DateTimeInterface $date
     *
     * @return bool
     */
    protected function checkTimeZone(\DateTimeInterface $date) {
        $timeZone = $date->getTimezone();
        $tzId = $timeZone->getName();

        // UTC must not be added
        if ('UTC' !== $tzId && !$this->hasTimeZone($tzId)) {
            $this->addTimeZone($tzId);

            return true;
        }

        return false;
    }

    /**
     * Checks if a time zone exists in current calendar
     *
     * @param string $tzId
     *
     * @return bool
     */
    protected function hasTimeZone($tzId) {
        $timeZones = $this->calendar->select('VTIMEZONE');

        foreach ($timeZones as $timeZone) {
            $zone = $timeZone->getTimeZone();

            if ($tzId === $zone->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds a new time zone to the corresponding calendar
     *
     * @param string $tzId
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.3
     */
    protected function addTimeZone($tzId) {
        $this->calendar->add(TimeZone::getTimeZone($tzId));
    }

    /**
     * Catch functions that not exist
     *
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, $arguments) {
        throw new \BadMethodCallException('There is no method named "' . $name . '" in this object!');
    }

    /**
     * Checks if the given status is valid for this entity
     *
     * @param string $status
     *
     * @return bool
     */
    abstract protected function isStatusValid($status);
}