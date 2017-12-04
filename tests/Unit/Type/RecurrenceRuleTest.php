<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Enum\RecurrenceFrequency;
use Brainformatik\CalDAV\Enum\RecurrenceWeekDay;
use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\RecurrenceRule;

class RecurrenceRuleTest extends BaseTestCase {

    /**
     * @var RecurrenceRule
     */
    protected $recurrenceRule;

    public function setUp() {
        $this->recurrenceRule = new RecurrenceRule();
    }

    public function testToString() {
        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEmpty($string);
    }

    public function testSetFrequency() {
        $this->assertException(function() {
            $this->recurrenceRule->setFrequency('UnknownFrequency');
        }, InvalidArgumentException::class, null, 'Value not in enum RecurrenceFrequency!');

        $this->recurrenceRule->setFrequency(RecurrenceFrequency::DAILY);
        $this->recurrenceRule->setCount(1);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);

        // must always be the first part in the generated string
        $this->assertTrue(0 === mb_strpos($string, 'FREQ=DAILY'));
    }

    public function testSetUntil() {
        $this->assertException(function() {
            $this->recurrenceRule->setUntil('2016-12-12 11:00:00');
        }, TypeError::class);

        $this->assertException(function() {
            $americanDateTime = new DateTime('2016-12-12 11:00:00', new DateTimeZone('America/New_York'));
            $this->recurrenceRule->setUntil($americanDateTime);
        }, InvalidArgumentException::class, null, 'Non floating date time value must have UTC time zone!');

        $this->recurrenceRule->setUntil(new DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('UNTIL=20161212T110000Z', $string);

        $this->recurrenceRule->setUntil(new DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')), true);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('UNTIL=20161212', $string);

        $this->recurrenceRule->setUntil(new DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')), false, true);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('UNTIL=20161212T110000', $string);
    }

    public function testSetCount() {
        $this->assertException(function() {
            $this->recurrenceRule->setCount('Some text');
        }, InvalidArgumentException::class, null, 'Count must be a positive integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setCount(false);
        }, InvalidArgumentException::class, null, 'Count must be a positive integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setCount(0);
        }, InvalidArgumentException::class, null, 'Count must be a positive integer!');

        $this->recurrenceRule->setCount(4);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('COUNT=4', $string);
    }

    public function testSetInterval() {
        $this->assertException(function() {
            $this->recurrenceRule->setInterval('Some text');
        }, InvalidArgumentException::class, null, 'Interval must be a positive integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setInterval(false);
        }, InvalidArgumentException::class, null, 'Interval must be a positive integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setInterval(0);
        }, InvalidArgumentException::class, null, 'Interval must be a positive integer!');

        $this->recurrenceRule->setInterval(4);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('INTERVAL=4', $string);
    }

    public function testSetSecondsList() {
        $this->assertException(function() {
            $this->recurrenceRule->setSecondsList(['Some text']);
        }, InvalidArgumentException::class, null, 'Each given second must be an integer between 0 and 60!');
        $this->assertException(function() {
            $this->recurrenceRule->setSecondsList([false]);
        }, InvalidArgumentException::class, null, 'Each given second must be an integer between 0 and 60!');
        $this->assertException(function() {
            $this->recurrenceRule->setSecondsList([-1]);
        }, InvalidArgumentException::class, null, 'Each given second must be an integer between 0 and 60!');
        $this->assertException(function() {
            $this->recurrenceRule->setSecondsList([61]);
        }, InvalidArgumentException::class, null, 'Each given second must be an integer between 0 and 60!');

        $this->recurrenceRule->setSecondsList([10,20,30]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYSECOND=10,20,30', $string);
    }

    public function testSetMinutesList() {
        $this->assertException(function() {
            $this->recurrenceRule->setMinutesList(['Some text']);
        }, InvalidArgumentException::class, null, 'Each given minute must be an integer between 0 and 59!');
        $this->assertException(function() {
            $this->recurrenceRule->setMinutesList([false]);
        }, InvalidArgumentException::class, null, 'Each given minute must be an integer between 0 and 59!');
        $this->assertException(function() {
            $this->recurrenceRule->setMinutesList([-1]);
        }, InvalidArgumentException::class, null, 'Each given minute must be an integer between 0 and 59!');
        $this->assertException(function() {
            $this->recurrenceRule->setMinutesList([61]);
        }, InvalidArgumentException::class, null, 'Each given minute must be an integer between 0 and 59!');

        $this->recurrenceRule->setMinutesList([10,20,30]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYMINUTE=10,20,30', $string);
    }

    public function testSetHoursList() {
        $this->assertException(function() {
            $this->recurrenceRule->setHoursList(['Some text']);
        }, InvalidArgumentException::class, null, 'Each given hour must be an integer between 0 and 23!');
        $this->assertException(function() {
            $this->recurrenceRule->setHoursList([false]);
        }, InvalidArgumentException::class, null, 'Each given hour must be an integer between 0 and 23!');
        $this->assertException(function() {
            $this->recurrenceRule->setHoursList([-1]);
        }, InvalidArgumentException::class, null, 'Each given hour must be an integer between 0 and 23!');
        $this->assertException(function() {
            $this->recurrenceRule->setHoursList([24]);
        }, InvalidArgumentException::class, null, 'Each given hour must be an integer between 0 and 23!');

        $this->recurrenceRule->setHoursList([2,4,6,8]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYHOUR=2,4,6,8', $string);
    }

    public function testSetWeekDaysList() {
        $this->assertException(function() {
            $this->recurrenceRule->setWeekDaysList(['Some text']);
        }, InvalidArgumentException::class, null, 'The given value is not within the allowed week days!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekDaysList([false]);
        }, InvalidArgumentException::class, null, 'Format for some of the given week days is not valid!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekDaysList([-1]);
        }, InvalidArgumentException::class, null, 'Format for some of the given week days is not valid!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekDaysList([61]);
        }, InvalidArgumentException::class, null, 'Format for some of the given week days is not valid!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekDaysList(['54TH']);
        }, OutOfRangeException::class, null, 'Prefix of week number must be between -53 and -1 or 1 and 53!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekDaysList(['-54TH']);
        }, OutOfRangeException::class, null, 'Prefix of week number must be between -53 and -1 or 1 and 53!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekDaysList(['0TH']);
        }, OutOfRangeException::class, null, 'Prefix of week number must be between -53 and -1 or 1 and 53!');

        $this->recurrenceRule->setWeekDaysList(['10MO','20FR','-30SA']);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYDAY=10MO,20FR,-30SA', $string);
    }

    public function testSetMonthDaysList() {
        $this->assertException(function() {
            $this->recurrenceRule->setMonthDaysList(['Some text']);
        }, InvalidArgumentException::class, null, 'Week numbers must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setMonthDaysList([false]);
        }, InvalidArgumentException::class, null, 'Week numbers must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setMonthDaysList([-32]);
        }, OutOfRangeException::class, null, 'Number of day must be between -31 and -1 or 1 and 31!');
        $this->assertException(function() {
            $this->recurrenceRule->setMonthDaysList([0]);
        }, OutOfRangeException::class, null, 'Number of day must be between -31 and -1 or 1 and 31!');
        $this->assertException(function() {
            $this->recurrenceRule->setMonthDaysList([32]);
        }, OutOfRangeException::class, null, 'Number of day must be between -31 and -1 or 1 and 31!');

        $this->recurrenceRule->setMonthDaysList([2,4,6,8]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYMONTHDAY=2,4,6,8', $string);
    }

    public function testSetYearDaysList() {
        $this->assertException(function() {
            $this->recurrenceRule->setYearDaysList(['Some text']);
        }, InvalidArgumentException::class, null, 'Day of year must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setYearDaysList([false]);
        }, InvalidArgumentException::class, null, 'Day of year must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setYearDaysList([-367]);
        }, OutOfRangeException::class, null, 'Number of day must be between -366 and -1 or 1 and 366!');
        $this->assertException(function() {
            $this->recurrenceRule->setYearDaysList([0]);
        }, OutOfRangeException::class, null, 'Number of day must be between -366 and -1 or 1 and 366!');
        $this->assertException(function() {
            $this->recurrenceRule->setYearDaysList([367]);
        }, OutOfRangeException::class, null, 'Number of day must be between -366 and -1 or 1 and 366!');

        $this->recurrenceRule->setYearDaysList([2,4,6,8]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYYEARDAY=2,4,6,8', $string);
    }

    public function testSetWeekNumbersList() {
        $this->assertException(function() {
            $this->recurrenceRule->setWeekNumbersList(['Some text']);
        }, InvalidArgumentException::class, null, 'Week numbers must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekNumbersList([false]);
        }, InvalidArgumentException::class, null, 'Week numbers must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekNumbersList([-54]);
        }, OutOfRangeException::class, null, 'Number of week must be between -53 and -1 or 1 and 53!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekNumbersList([0]);
        }, OutOfRangeException::class, null, 'Number of week must be between -53 and -1 or 1 and 53!');
        $this->assertException(function() {
            $this->recurrenceRule->setWeekNumbersList([54]);
        }, OutOfRangeException::class, null, 'Number of week must be between -53 and -1 or 1 and 53!');

        $this->recurrenceRule->setWeekNumbersList([2,4,6,8]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYWEEKNO=2,4,6,8', $string);
    }

    public function testSetMonthsList() {
        $this->assertException(function() {
            $this->recurrenceRule->setMonthsList(['Some text']);
        }, InvalidArgumentException::class, null, 'Each given month must be an integer between 1 and 12!');
        $this->assertException(function() {
            $this->recurrenceRule->setMonthsList([false]);
        }, InvalidArgumentException::class, null, 'Each given month must be an integer between 1 and 12!');
        $this->assertException(function() {
            $this->recurrenceRule->setMonthsList([0]);
        }, InvalidArgumentException::class, null, 'Each given month must be an integer between 1 and 12!');
        $this->assertException(function() {
            $this->recurrenceRule->setMonthsList([13]);
        }, InvalidArgumentException::class, null, 'Each given month must be an integer between 1 and 12!');

        $this->recurrenceRule->setMonthsList([2,4,6,8,10,12]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYMONTH=2,4,6,8,10,12', $string);
    }

    public function testSetPositionList() {
        $this->assertException(function() {
            $this->recurrenceRule->setPositionList(['Some text']);
        }, InvalidArgumentException::class, null, 'Position must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setPositionList([false]);
        }, InvalidArgumentException::class, null, 'Position must be integer!');
        $this->assertException(function() {
            $this->recurrenceRule->setPositionList([-367]);
        }, OutOfRangeException::class, null, 'Position must be between -366 and -1 or 1 and 366!');
        $this->assertException(function() {
            $this->recurrenceRule->setPositionList([0]);
        }, OutOfRangeException::class, null, 'Position must be between -366 and -1 or 1 and 366!');
        $this->assertException(function() {
            $this->recurrenceRule->setPositionList([367]);
        }, OutOfRangeException::class, null, 'Position must be between -366 and -1 or 1 and 366!');

        $this->recurrenceRule->setPositionList([2,4,6,8]);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('BYSETPOS=2,4,6,8', $string);
    }

    public function testSetWeekStartDay() {
        $this->assertException(function() {
            $this->recurrenceRule->setWeekStartDay('UnknownDay');
        }, InvalidArgumentException::class, null, 'Week day is not in enum RecurrenceWeekDay!');

        $this->recurrenceRule->setWeekStartDay(RecurrenceWeekDay::MONDAY);

        $string = $this->recurrenceRule->toString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('WKST=MO', $string);
    }
}
