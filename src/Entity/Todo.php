<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Entity;

use Brainformatik\CalDAV\Enum\TodoStatus;
use Sabre\VObject\Component\VCalendar;

class Todo extends AbstractEntity {

    /**
     * @var string
     */
    protected $type = 'Todo';

    /**
     * @param VCalendar $calendar
     */
    public function __construct(VCalendar $calendar) {
        $this->calendar = $calendar;
        $this->instance = $this->calendar->add('VTODO', []);
        $this->instance->select('DTSTAMP')[0]->setValue(new \DateTime('now', new \DateTimeZone('UTC')));
    }

    /**
     * This property is used by an assignee or delegatee of a to-do to convey the percent completion
     *
     * @param int    $percentComplete
     * @param array  $parameters
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.8
     */
    public function setPercentComplete($percentComplete, array $parameters = null) {

        if (!is_int($percentComplete)) {
            throw new \InvalidArgumentException('Percentage should be an integer value!');
        }

        if (0 > $percentComplete || $percentComplete > 100) {
            throw new \OutOfRangeException('Percentage must be between 0 and 100!');
        }

        $this->instance->add($this->calendar->createProperty('PERCENT-COMPLETE', $percentComplete, $parameters));
    }

    /**
     * This property defines the date and time that a to-do was actually completed
     *
     * This value has to use time zone UTC
     *
     * @param \DateTimeInterface $completed
     * @param array              $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.1
     */
    public function setCompleted(\DateTimeInterface $completed, array $parameters = null) {
        $timeZone = $completed->getTimezone();

        if ('UTC' !== $timeZone->getName()) {
            throw new \InvalidArgumentException('The value must use UTC as time zone!');
        }

        $this->instance->add($this->calendar->createProperty('COMPLETED', $completed, $parameters));
    }

    /**
     * This property defines the date and time that a to-do is expected to be completed
     *
     * Time zone of the given parameter should be explicitly set. Otherwise time zone of server will be used.
     *
     * Note: If you only want to use the date part you can set parameter VALUE => DATE
     *
     * @param \DateTimeInterface $due
     * @param bool               $isFloating
     * @param array              $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.3
     */
    public function setDue(\DateTimeInterface $due, $isFloating = false, array $parameters = null) {
        $this->instance->add($this->createDateProperty('DUE', $due, $isFloating, $parameters));
    }

    /**
     * Checks if the given status is valid for this entity
     *
     * @param string $status
     *
     * @return bool
     */
    protected function isStatusValid($status) {
        return TodoStatus::has($status);
    }
}