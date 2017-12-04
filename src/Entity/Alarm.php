<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Entity;

use Brainformatik\CalDAV\Enum\Action;
use Brainformatik\CalDAV\Helper\TimeZone;
use Brainformatik\CalDAV\Type\Attendee;
use Brainformatik\CalDAV\Type\Duration;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Component\VAlarm;

class Alarm {
    
    /**
     * @var VAlarm
     */
    protected $instance;
    
    /**
     * @var VCalendar
     */
    protected $calendar;
    
    public function __construct(VCalendar $calendar) {
        $this->calendar = $calendar;
        $this->instance = $this->calendar->add('VALARM');
    }
    
    /**
     * This property specifies a trigger
     *
     * @param Duration $duration
     * @param array $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.5
     */
    public function setTrigger(Duration $duration, array $parameters = null) {
        $this->instance->add($this->calendar->createProperty('TRIGGER', $duration->toString(), $parameters));
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
     * Defines action for alarm
     *
     * @param Action     $action
     * @param array|null $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.6.1
     */
    public function setAction($action, array $parameters = null) {
        
        if (!Action::has($action)) {
            throw new \InvalidArgumentException('This action is not allowed for current entity!');
        }
        
        $this->instance->add($this->calendar->createProperty('ACTION', $action, $parameters));
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
     * This property specifies the repeat count
     *
     * @param int   $repeatCount
     * @param array $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.6.2
     */
    public function setRepeatCount($repeatCount, array $parameters = null) {
        if (!is_int($repeatCount)) {
            throw new \InvalidArgumentException('repeat count should be an integer value!');
        }
        
        if (0 >= $repeatCount) {
            throw new \OutOfRangeException('repeat count should be greater than 0!');
        }
        
        $this->instance->add($this->calendar->createProperty('REPEAT', $repeatCount, $parameters));
    }
    
    /**
     * This property defines an "Attendee"
     *
     * @param Attendee $attendee
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.1
     */
    public function addAttendee(Attendee $attendee) {
        $this->instance->add($this->calendar->createProperty('ATTENDEE', $attendee->getAddress(), $attendee->getParameters()));
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
    
}