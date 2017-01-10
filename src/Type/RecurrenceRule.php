<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Type;

use Brainformatik\CalDAV\Enum\RecurrenceFrequency;
use Brainformatik\CalDAV\Enum\RecurrenceWeekDay;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Property\ICalendar\DateTime;

/**
 * Class RecurrenceRule
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.3.10
 *
 * @package Brainformatik\CalDAV\Type
 */
class RecurrenceRule implements StringTypeInterface {

    /**
     * Contains parts that are set to the rule
     *
     * @var array
     */
    protected $parts = [];

    /**
     * Returns the string representation of the object
     *
     * @return string - representation of the type according to RFC5545
     *
     * @throws \UnexpectedValueException - In case an attribute has a wrong value
     */
    public function toString() {
        $ruleParts = [];

        foreach ($this->parts as $key => $value) {

            $ruleParts[] = $key . '=' . (is_array($value) ? implode(',', $value) : $value);
        }

        return implode(';', $ruleParts);
    }

    /**
     * The FREQ rule part identifies the type of recurrence rule
     *
     * @param string $frequency
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setFrequency($frequency) {

        if (!RecurrenceFrequency::has($frequency)) {
            throw new \InvalidArgumentException('Value not in enum RecurrenceFrequency!');
        }

        // make sure that FREQ is always the first element in the parts array as defined in RFC5545
        $this->parts = array_merge(['FREQ' => $frequency], $this->parts);

        return $this;
    }

    /**
     * The UNTIL rule part defines a DATE or DATE-TIME value that bounds the recurrence rule in an inclusive manner
     *
     * Note: Non floating date time value must have UTC time zone
     *
     * @param \DateTimeInterface $until
     * @param bool               $onlyDate
     * @param bool               $isFloating
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setUntil(\DateTimeInterface $until, $onlyDate = false, $isFloating = false) {
        $timeZone = $until->getTimezone();
        $timeZoneName = $timeZone->getName();

        // if value contains time and is not floating (local time) than time zone must be UTC according to RFC5545
        if (false === $isFloating && 'UTC' !== $timeZoneName && false === $onlyDate) {
            throw new \InvalidArgumentException('Non floating date time value must have UTC time zone!');
        }

        $parameters = $onlyDate ? ['VALUE' => 'DATE'] : [];
        $dateTime = new DateTime(new VCalendar(), '', null, $parameters);
        $dateTime->setDateTime($until, $isFloating);

        $this->parts['UNTIL'] = $dateTime->getValue();

        return $this;
    }

    /**
     * The COUNT rule part defines the number of occurrences at which to range-bound the recurrence
     *
     * The "DTSTART" property value always counts as the first occurrence
     *
     * @param int $count
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setCount($count) {

        if (!is_int($count) || is_int($count) && $count < 1) {
            throw new \InvalidArgumentException('Count must be a positive integer!');
        }

        $this->parts['COUNT'] = $count;

        return $this;
    }

    /**
     * The INTERVAL rule part contains a positive integer representing at which intervals the recurrence rule repeats.
     *
     * The default value is "1", meaning every second for a SECONDLY rule, every minute for a MINUTELY rule,
     * every hour for an HOURLY rule, every day for a DAILY rule, every week for a WEEKLY rule, every month for a
     * MONTHLY rule, and every year for a YEARLY rule.
     *
     * @param int $interval
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setInterval($interval) {

        if (!is_int($interval) || is_int($interval) && $interval < 1) {
            throw new \InvalidArgumentException('Interval must be a positive integer!');
        }

        $this->parts['INTERVAL'] = $interval;

        return $this;
    }

    /**
     * The BYSECOND rule part specifies a COMMA-separated list of seconds within a minute
     *
     * Note: Valid values are 0 to 60
     *
     * @param array $seconds
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setSecondsList(array $seconds) {

        foreach ($seconds as $second) {

            if (!is_int($second) || is_int($second) && (0 > $second || $second > 60)) {
                throw new \InvalidArgumentException('Each given second must be an integer between 0 and 60!');
            }
        }

        $this->parts['BYSECOND'] = implode(',', array_unique($seconds));

        return $this;
    }

    /**
     * The BYMINUTE rule part specifies a COMMA-separated list of minutes within an hour
     *
     * Note: Valid values are 0 to 59
     *
     * @param array $minutes
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setMinutesList(array $minutes) {

        foreach ($minutes as $minute) {

            if (!is_int($minute) || is_int($minute) && (0 > $minute || $minute > 59)) {
                throw new \InvalidArgumentException('Each given minute must be an integer between 0 and 59!');
            }
        }

        $this->parts['BYMINUTE'] = implode(',', array_unique($minutes));

        return $this;
    }

    /**
     * The BYHOUR rule part specifies a COMMA-separated list of hours of the day
     *
     * Note: Valid values are 0 to 23
     *
     * @param array $hours
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setHoursList(array $hours) {

        foreach ($hours as $hour) {

            if (!is_int($hour) || is_int($hour) && (0 > $hour || $hour > 23)) {
                throw new \InvalidArgumentException('Each given hour must be an integer between 0 and 23!');
            }
        }

        $this->parts['BYHOUR'] = implode(',', array_unique($hours));

        return $this;
    }

    /**
     * The BYDAY rule part specifies a COMMA-separated list of days of the week
     *
     * @param array $weekDays
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function setWeekDaysList(array $weekDays) {
        $weekDaysString = implode(',', $weekDays);

        preg_match_all('/([+\-]?\d{1,2})?([A-Z]{1,2})/', $weekDaysString, $matches);

        if (count($matches[0]) !== count($weekDays)) {
            throw new \InvalidArgumentException('Format for some of the given week days is not valid!');
        }

        foreach ($weekDays as $index => $weekDay) {
            $weekNumberPrefix = (int) $matches[1][$index];
            $weekDay = $matches[2][$index];

            if (-53 > $weekNumberPrefix || (0 === $weekNumberPrefix && '' !== $matches[1][$index]) || $weekNumberPrefix > 53) {
                throw new \OutOfRangeException('Prefix of week number must be between -53 and -1 or 1 and 53!');
            }

            if (!RecurrenceWeekDay::has($weekDay)) {
                throw new \InvalidArgumentException('The given value is not within the allowed week days!');
            }
        }

        $this->parts['BYDAY'] = $weekDaysString;

        return $this;
    }

    /**
     * The BYMONTHDAY rule part specifies a COMMA-separated list of days of the month
     *
     * @param array $monthDays
     *
     * @return RecurrenceRule
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function setMonthDaysList(array $monthDays) {

        foreach ($monthDays as $monthDay) {

            if (!is_int($monthDay)) {
                throw new \InvalidArgumentException('Week numbers must be integer!');
            }

            if (-31 > $monthDay || 0 === $monthDay || $monthDay > 31) {
                throw new \OutOfRangeException('Number of day must be between -31 and -1 or 1 and 31!');
            }
        }

        $this->parts['BYMONTHDAY'] = array_unique($monthDays);

        return $this;
    }

    /**
     * The BYYEARDAY rule part specifies a COMMA-separated list of days of the year
     *
     * @param array $yearDays
     *
     * @return RecurrenceRule
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function setYearDaysList(array $yearDays) {

        foreach ($yearDays as $yearDay) {

            if (!is_int($yearDay)) {
                throw new \InvalidArgumentException('Day of year must be integer!');
            }

            if (-366 > $yearDay || 0 === $yearDay || $yearDay > 366) {
                throw new \OutOfRangeException('Number of day must be between -366 and -1 or 1 and 366!');
            }
        }

        $this->parts['BYYEARDAY'] = array_unique($yearDays);

        return $this;
    }

    /**
     * The BYWEEKNO rule part specifies a COMMA-separated list of ordinals specifying weeks of the year
     *
     * @param array $weekNumbers
     *
     * @return RecurrenceRule
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function setWeekNumbersList(array $weekNumbers) {

        foreach ($weekNumbers as $weekNumber) {

            if (!is_int($weekNumber)) {
                throw new \InvalidArgumentException('Week numbers must be integer!');
            }

            if (-53 > $weekNumber || 0 === $weekNumber || $weekNumber > 53) {
                throw new \OutOfRangeException('Number of week must be between -53 and -1 or 1 and 53!');
            }
        }

        $this->parts['BYWEEKNO'] = array_unique($weekNumbers);

        return $this;
    }

    /**
     * The BYMONTH rule part specifies a COMMA-separated list of months of the year
     *
     * Note: Valid values are 1 to 12
     *
     * @param array $months
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setMonthsList(array $months) {

        foreach ($months as $month) {

            if (!is_int($month) || is_int($month) && (1 > $month || $month > 12)) {
                throw new \InvalidArgumentException('Each given month must be an integer between 1 and 12!');
            }
        }

        $this->parts['BYMONTH'] = implode(',', array_unique($months));

        return $this;
    }

    /**
     * The BYSETPOS rule part specifies a COMMA-separated list of values that corresponds to the nth occurrence
     * within the set of recurrence instances specified by the rule
     *
     * @param array $positions
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setPositionList(array $positions) {

        foreach ($positions as $position) {

            if (!is_int($position)) {
                throw new \InvalidArgumentException('Position must be integer!');
            }

            if (-366 > $position || 0 === $position || $position > 366) {
                throw new \OutOfRangeException('Position must be between -366 and -1 or 1 and 366!');
            }
        }

        $this->parts['BYSETPOS'] = implode(',', array_unique($positions));

        return $this;
    }

    /**
     * The WKST rule part specifies the day on which the workweek starts
     *
     * @param string $weekDay
     *
     * @return RecurrenceRule
     *
     * @throws \InvalidArgumentException
     */
    public function setWeekStartDay($weekDay) {

        if (!RecurrenceWeekDay::has($weekDay)) {
            throw new \InvalidArgumentException('Week day is not in enum RecurrenceWeekDay!');
        }

        $this->parts['WKST'] = $weekDay;

        return $this;
    }
}