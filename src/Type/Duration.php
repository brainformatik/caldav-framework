<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Type;

/**
 * Class Duration
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.3.6
 *
 * @package Brainformatik\CalDAV\Type
 *
 */
class Duration implements StringTypeInterface {

    /**
     * @var int
     */
    protected $day = 0;

    /**
     * @var int
     */
    protected $hour = 0;

    /**
     * @var int
     */
    protected $minute = 0;

    /**
     * @var int
     */
    protected $second = 0;

    /**
     * @var int
     */
    protected $week = 0;

    /**
     * Returns the string representation of the object
     *
     * @return string - representation of the type according to RFC5545
     *
     * @throws \UnexpectedValueException - In case an attribute has a wrong value
     */
    public function toString() {

        if (0 === $this->day && 0 === $this->hour && 0 === $this->minute && 0 === $this->second && 0 === $this->week) {
            throw new \UnexpectedValueException('At least one duration value must be set!');
        }

        $duration = 'P';

        if (0 !== $this->week) {
            $duration .= $this->week . 'W';
        } else {

            if (0 !== $this->day) {
                $duration .= $this->day . 'D';
            }

            if (0 !== $this->hour || 0 !== $this->minute || 0 !== $this->second) {
                $duration .= 'T';
            }

            if (0 !== $this->hour) {
                $duration .= $this->hour . 'H';
            }

            if (0 !== $this->minute) {
                $duration .= $this->minute . 'M';
            }

            if (0 !== $this->second) {
                $duration .= $this->second . 'S';
            }
        }

        return $duration;
    }

    /**
     * @param int $day
     *
     * @return Duration
     */
    public function setDay($day) {

        if (!is_int($day) || is_int($day) && $day <= 0) {
            throw new \InvalidArgumentException('Value for day must be integer greater than 0!');
        }

        $this->day = $day;

        return $this;
    }

    /**
     * @param int $hour
     *
     * @return Duration
     */
    public function setHour($hour) {

        if (!is_int($hour) || is_int($hour) && $hour <= 0) {
            throw new \InvalidArgumentException('Value for hour must be integer greater than 0!');
        }

        $this->hour = $hour;

        return $this;
    }

    /**
     * @param int $minute
     *
     * @return Duration
     */
    public function setMinute($minute) {

        if (!is_int($minute) || is_int($minute) && $minute <= 0) {
            throw new \InvalidArgumentException('Value for minute must be integer greater than 0!');
        }

        $this->minute = $minute;

        return $this;
    }

    /**
     * @param int $second
     *
     * @return Duration
     */
    public function setSecond($second) {

        if (!is_int($second) || is_int($second) && $second <= 0) {
            throw new \InvalidArgumentException('Value for second must be integer greater than 0!');
        }

        $this->second = $second;

        return $this;
    }

    /**
     * @param int $week
     *
     * @return Duration
     */
    public function setWeek($week) {

        if (!is_int($week) || is_int($week) && $week <= 0) {
            throw new \InvalidArgumentException('Value for week must be integer greater than 0!');
        }

        $this->week = $week;

        return $this;
    }
}