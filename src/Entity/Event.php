<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Entity;

use Brainformatik\CalDAV\Enum\EventStatus;
use Brainformatik\CalDAV\Enum\EventTransparency;
use Sabre\VObject\Component\VCalendar;

class Event extends AbstractEntity {

    /**
     * @var string
     */
    protected $type = 'Event';

    /**
     * @param VCalendar $calendar
     */
    public function __construct(VCalendar $calendar) {
        $this->calendar = $calendar;
        $this->instance = $this->calendar->add('VEVENT', []);
        $this->instance->select('DTSTAMP')[0]->setValue(new \DateTime('now', new \DateTimeZone('UTC')));
    }

    /**
     * This property specifies the date and time that a calendar component ends
     *
     * Time zone of the given parameter should be explicitly set. Otherwise time zone of server will be used.
     *
     * Note: If you only want to use the date part you can set parameter VALUE => DATE
     *
     * @param \DateTimeInterface $end
     * @param bool               $isFloating
     * @param array              $parameters
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.2
     */
    public function setDateEnd(\DateTimeInterface $end, $isFloating = false, array $parameters = null) {
        $this->instance->add($this->createDateProperty('DTEND', $end, $isFloating, $parameters));
    }

    /**
     * This property defines whether or not an event is transparent to busy time searches
     *
     * @param string $transparency - Value of enum EventTransparency
     * @param array  $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.7
     */
    public function setTransparency($transparency, array $parameters = null) {

        if (!EventTransparency::has($transparency)) {
            throw new \InvalidArgumentException('This transparency is not allowed for current entity!');
        }

        $this->instance->add($this->calendar->createProperty('TRANSP', $transparency, $parameters));
    }

    /**
     * Checks if the given status is valid for this entity
     *
     * @param string $status
     *
     * @return bool
     */
    protected function isStatusValid($status) {
        return EventStatus::has($status);
    }
}