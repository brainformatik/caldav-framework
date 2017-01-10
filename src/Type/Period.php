<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Type;

use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Property\ICalendar\DateTime;

/**
 * Class Period
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.3.9
 *
 * @package Brainformatik\CalDAV\Type
 */
class Period implements StringTypeInterface {

    /**
     * @var DateTime
     */
    protected $start;

    /**
     * @var DateTime
     */
    protected $end;

    /**
     * @var Duration
     */
    protected $duration;

    /**
     * Returns the string representation of the object
     *
     * Note: End is preferred over duration if both are set
     *
     * @return string - representation of the type according to RFC5545
     *
     * @throws \UnexpectedValueException - In case an attribute has a wrong value
     */
    public function toString() {

        if (!($this->start instanceof DateTime)) {
            throw new \UnexpectedValueException('No start date time is set!');
        }

        if (!($this->end instanceof DateTime) && !($this->duration instanceof Duration)) {
            throw new \UnexpectedValueException('Either end date time or duration must be set!');
        }

        $period = $this->start->getValue() . '/';

        if ($this->end instanceof DateTime) {
            $period .= $this->end->getValue();
        } else if ($this->duration instanceof Duration) {
            $period .= $this->duration->toString();
        }

        return $period;
    }

    /**
     * Sets the start date of the period
     *
     * @param \DateTimeInterface $start
     *
     * @return Period
     *
     * @throws \InvalidArgumentException
     */
    public function setStart(\DateTimeInterface $start) {
        $timeZone = $start->getTimezone();
        $timeZoneName = $timeZone->getName();

        if ('UTC' !== $timeZoneName) {
            throw new \InvalidArgumentException('Time zone of start must be UTC!');
        }

        $this->start = new DateTime(new VCalendar(), '', $start);

        return $this;
    }

    /**
     * Sets the end date of the period
     *
     * Note: End is preferred over duration if both are set
     *
     * @param \DateTimeInterface $end
     *
     * @return Period
     *
     * @throws \InvalidArgumentException
     */
    public function setEnd(\DateTimeInterface $end) {
        $timeZone = $end->getTimezone();
        $timeZoneName = $timeZone->getName();

        if ('UTC' !== $timeZoneName) {
            throw new \InvalidArgumentException('Time zone of end must be UTC!');
        }

        $this->end = new DateTime(new VCalendar(), '', $end);

        return $this;
    }

    /**
     * Sets the duration of the period
     *
     * Note: End is preferred over duration if both are set
     *
     * @param Duration $duration
     *
     * @return Period
     */
    public function setDuration(Duration $duration) {
        $this->duration = $duration;

        return $this;
    }
}