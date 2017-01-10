<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Entity\Todo;
use Brainformatik\CalDAV\Enum\EventStatus;
use Brainformatik\CalDAV\Enum\RecurrenceFrequency;
use Brainformatik\CalDAV\Enum\TodoStatus;
use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\Attendee;
use Brainformatik\CalDAV\Type\Contact;
use Brainformatik\CalDAV\Type\Duration;
use Brainformatik\CalDAV\Type\Organizer;
use Brainformatik\CalDAV\Type\Period;
use Brainformatik\CalDAV\Type\RecurrenceRule;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Component\VTodo;
use Sabre\VObject\Property;
use Sabre\VObject\Property\ICalendar\DateTime;

class TodoTest extends BaseTestCase {

    /**
     * @var VCalendar
     */
    protected $calendar;

    /**
     * @var Todo
     */
    protected $todo;

    public function setUp() {
        $this->calendar = new VCalendar();
        $this->todo = new Todo($this->calendar);
    }

    /*
     * The following tests are only for To-do
     */

    public function testConstruct() {
        $this->assertException(function() {
            new Todo('Calendar');
        }, PHPUnit_Framework_Error::class);

        $event = new Todo($this->calendar);

        $vTodo = $this->invokeProperty($event, 'instance');

        $this->assertTrue($vTodo instanceof VTodo);

        $instanceType = $this->invokeProperty($event, 'type');

        $this->assertEquals('Todo', $instanceType);
    }

    public function testSetPercentComplete() {
        $this->assertException(function() {
            $this->todo->setPercentComplete('Some text');
        }, InvalidArgumentException::class, null, 'Percentage should be an integer value!');
        $this->assertException(function() {
            $this->todo->setPercentComplete(false);
        }, InvalidArgumentException::class, null, 'Percentage should be an integer value!');
        $this->assertException(function() {
            $this->todo->setPercentComplete(-1);
        }, OutOfRangeException::class, null, 'Percentage must be between 0 and 100!');
        $this->assertException(function() {
            $this->todo->setPercentComplete(101);
        }, OutOfRangeException::class, null, 'Percentage must be between 0 and 100!');

        $this->todo->setPercentComplete(4);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('PERCENT-COMPLETE:4', $iCalendarString);
    }

    public function testSetCompleted() {
        $this->assertException(function() {
            $this->todo->setCompleted('2016-12-12 11:00:00');
        }, PHPUnit_Framework_Error::class);

        $this->assertException(function() {
            $this->todo->setCompleted(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));
        }, InvalidArgumentException::class, null, 'The value must use UTC as time zone!');

        $this->todo->setCompleted(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("COMPLETED:20161212T110000Z\r\n", $iCalendarString);
    }

    public function testSetDue() {
        $this->assertException(function() {
            $this->todo->setDue('2016-12-12 11:00:00');
        }, PHPUnit_Framework_Error::class);

        // test date time with time zone
        $this->todo->setDue(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DUE;TZID=Europe/Berlin:20161212T110000\r\n", $iCalendarString);

        // test if automatic embedding of time zone works correctly
        $this->assertContains("BEGIN:VTIMEZONE\r\nTZID:Europe/Berlin", $iCalendarString);

        // test floating time
        $this->todo->setDue(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), true);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DUE:20161212T110000\r\n", $iCalendarString);

        // test utc time zone
        $this->todo->setDue(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DUE:20161212T110000Z\r\n", $iCalendarString);

        // test date without time
        $this->todo->setDue(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), false, ['VALUE' => 'DATE']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DUE;VALUE=DATE:20161212\r\n", $iCalendarString);
    }

    public function testIsStatusValid() {
        $this->assertFalse($this->invokeMethod($this->todo, 'isStatusValid', [EventStatus::CONFIRMED]));
        $this->assertTrue($this->invokeMethod($this->todo, 'isStatusValid', [TodoStatus::IN_PROCESS]));
    }

    /*
     * The following tests are for AbstractEntity methods
     */

    public function testSetUid() {
        $this->todo->setUid('123456');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('UID:123456', $iCalendarString);
    }

    public function testSetSummary() {
        $this->todo->setSummary('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('SUMMARY:', $iCalendarString);

        $this->todo->setSummary('My first event');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('SUMMARY:My first event', $iCalendarString);
    }

    public function testSetDescription() {
        $this->todo->setDescription('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('DESCRIPTION:', $iCalendarString);

        $this->todo->setDescription('Description for my first event');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('DESCRIPTION:Description for my first event', $iCalendarString);
    }

    public function testSetComment() {
        $this->todo->setComment('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('COMMENT:', $iCalendarString);

        $this->todo->setComment('Comment for my first event');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('COMMENT:Comment for my first event', $iCalendarString);
    }

    public function testAddAttachment() {
        $this->todo->addAttachment('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('ATTACH:', $iCalendarString);

        $this->todo->addAttachment('http://domain.tdn/myImage.gif');
        $this->todo->addAttachment('http://domain.tdn/myImage.png');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('ATTACH:http://domain.tdn/myImage.gif', $iCalendarString);
        $this->assertContains('ATTACH:http://domain.tdn/myImage.png', $iCalendarString);
    }

    public function testAddCategories() {
        $this->todo->addCategories([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('CATEGORIES:', $iCalendarString);

        $this->todo->addCategories(['FIRST', 'SECOND', 'THIRD']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('CATEGORIES:FIRST,SECOND,THIRD', $iCalendarString);
    }

    public function testSetClass() {
        $this->todo->setClass('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('CLASS:', $iCalendarString);

        $this->todo->setClass('PRIVATE');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('CLASS:PRIVATE', $iCalendarString);
    }

    public function testSetGeo() {
        $this->assertException(function() {
            $this->todo->setGeo([5]);
        }, InvalidArgumentException::class, null, 'The array should contain two numbers!');
        $this->assertException(function() {
            $this->todo->setGeo([5, false]);
        }, InvalidArgumentException::class, null, 'The array should contain two numbers!');
        $this->assertException(function() {
            $this->todo->setGeo([4, 'Some text']);
        }, InvalidArgumentException::class, null, 'The array should contain two numbers!');

        $this->todo->setGeo([20.25, 45.28]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('GEO:20.25;45.28', $iCalendarString);
    }

    public function testSetLocation() {
        $this->todo->setLocation('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('LOCATION:', $iCalendarString);

        $this->todo->setLocation('Meeting room');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('LOCATION:Meeting room', $iCalendarString);
    }

    public function testSetPriority() {
        $this->assertException(function() {
            $this->todo->setPriority('Some text');
        }, InvalidArgumentException::class, null, 'Priority should be an integer value!');
        $this->assertException(function() {
            $this->todo->setPriority(false);
        }, InvalidArgumentException::class, null, 'Priority should be an integer value!');
        $this->assertException(function() {
            $this->todo->setPriority(-1);
        }, OutOfRangeException::class, null, 'Priority should be between 0 and 9!');
        $this->assertException(function() {
            $this->todo->setPriority(10);
        }, OutOfRangeException::class, null, 'Priority should be between 0 and 9!');

        $this->todo->setPriority(4);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('PRIORITY:4', $iCalendarString);
    }

    public function testAddResources() {
        $this->todo->addResources([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RESOURCES:', $iCalendarString);

        $this->todo->addResources(['FIRST', 'SECOND', 'THIRD']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RESOURCES:FIRST,SECOND,THIRD', $iCalendarString);
    }

    public function testSetStatus() {
        $this->assertException(function() {
            $this->todo->setStatus('UnknownStatus');
        }, InvalidArgumentException::class, null, 'This status is not allowed for current entity!');
        $this->assertException(function() {
            $this->todo->setStatus(EventStatus::CONFIRMED);
        }, InvalidArgumentException::class, null, 'This status is not allowed for current entity!');

        $this->todo->setStatus(TodoStatus::IN_PROCESS);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('STATUS:IN-PROCESS', $iCalendarString);
    }

    public function testSetDateStart() {
        $this->assertException(function() {
            $this->todo->setDateStart('2016-12-12 11:00:00');
        }, PHPUnit_Framework_Error::class);

        // test date time with time zone
        $this->todo->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART;TZID=Europe/Berlin:20161212T110000\r\n", $iCalendarString);

        // test if automatic embedding of time zone works correctly
        $this->assertContains("BEGIN:VTIMEZONE\r\nTZID:Europe/Berlin", $iCalendarString);

        // test floating time
        $this->todo->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), true);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART:20161212T110000\r\n", $iCalendarString);

        // test utc time zone
        $this->todo->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART:20161212T110000Z\r\n", $iCalendarString);

        // test date without time
        $this->todo->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), false, ['VALUE' => 'DATE']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART;VALUE=DATE:20161212\r\n", $iCalendarString);
    }

    public function testSetDuration() {
        $this->assertException(function() {
            $this->todo->setDuration('P4W');
        }, PHPUnit_Framework_Error::class);

        $duration = new Duration();
        $duration->setWeek(5);

        $this->todo->setDuration($duration);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DURATION:P5W\r\n", $iCalendarString);
    }

    public function testSetRelatedTo() {
        $this->todo->setRelatedTo('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RELATED-TO:', $iCalendarString);

        $this->todo->setRelatedTo('56789');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RELATED-TO:56789', $iCalendarString);

        $this->todo->setRelatedTo('123456', ['RELTYPE' => 'CHILD']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RELATED-TO;RELTYPE=CHILD:123456', $iCalendarString);
    }

    public function testSetUrl() {
        $this->todo->setUrl('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('URL:', $iCalendarString);

        $this->todo->setUrl('http://domain.tdn');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('URL;VALUE=URI:http://domain.tdn', $iCalendarString);
    }

    public function testAddAttendee() {
        $this->assertException(function() {
            $this->todo->addAttendee('John Doe');
        }, PHPUnit_Framework_Error::class);

        $this->assertException(function() {
            $wrongAttendeeType = new Attendee('address@domain.tdn', 'Event');
            $this->todo->addAttendee($wrongAttendeeType);
        }, InvalidArgumentException::class, null, 'Type of entity and target entity type of attendee must match!');

        $attendee = new Attendee('address@domain.tdn', 'Todo');

        $this->todo->addAttendee($attendee);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('ATTENDEE:mailto:address@domain.tdn', $iCalendarString);
    }

    public function testSetContact() {
        $this->assertException(function() {
            $this->todo->setContact('John Doe');
        }, PHPUnit_Framework_Error::class);

        $contact = new Contact('John Doe');

        $this->todo->setContact($contact);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('CONTACT:John Doe', $iCalendarString);
    }

    public function testSetOrganizer() {
        $this->assertException(function() {
            $this->todo->setOrganizer('John Doe');
        }, PHPUnit_Framework_Error::class);

        $organizer = new Organizer('address@domain.tdn');

        $this->todo->setOrganizer($organizer);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('ORGANIZER:mailto:address@domain.tdn', $iCalendarString);
    }

    public function testSetDateCreated() {
        $this->assertException(function() {
            $this->todo->setDateCreated('2016-12-12 11:00:00');
        }, PHPUnit_Framework_Error::class);

        $this->assertException(function() {
            $this->todo->setDateCreated(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));
        }, InvalidArgumentException::class, null, 'The value must use UTC as time zone!');

        $this->todo->setDateCreated(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("CREATED:20161212T110000Z\r\n", $iCalendarString);
    }

    public function testSetDateLastModified() {
        $this->assertException(function() {
            $this->todo->setDateLastModified('2016-12-12 11:00:00');
        }, PHPUnit_Framework_Error::class);

        $this->assertException(function() {
            $this->todo->setDateLastModified(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));
        }, InvalidArgumentException::class, null, 'The value must use UTC as time zone!');

        $this->todo->setDateLastModified(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("MODIFIED:20161212T110000Z\r\n", $iCalendarString);
    }

    public function testSetSequence() {
        $this->assertException(function() {
            $this->todo->setSequence('Some text');
        }, InvalidArgumentException::class, null, 'Sequence should be an integer value!');
        $this->assertException(function() {
            $this->todo->setSequence(false);
        }, InvalidArgumentException::class, null, 'Sequence should be an integer value!');

        $this->todo->setSequence(1);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("SEQUENCE:1\r\n", $iCalendarString);
    }

    public function testSetRecurrenceId() {
        $this->todo->setRecurrenceId('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RECURRENCE-ID:', $iCalendarString);

        $this->todo->setRecurrenceId('123456');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RECURRENCE-ID:123456', $iCalendarString);
    }

    public function testAddExceptionDates() {
        $this->todo->addExceptionDates([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('EXDATE:', $iCalendarString);

        $this->todo->addExceptionDates([
            new \DateTime('2015-12-12 12:00:00', new DateTimeZone('America/New_York')),
            new \DateTime('2015-12-14 12:00:00', new DateTimeZone('Europe/Berlin'))
        ]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('EXDATE;TZID=America/New_York:20151212T120000,20151214T060000', $iCalendarString);
    }

    public function testAddRecurrenceDates() {
        $this->assertException(function() {
            $this->todo->addRecurrenceDates([], ['VALUE' => 'PERIOD']);
        }, InvalidArgumentException::class, null, 'You have to use addRecurrencePeriods to set periods!');

        $this->todo->addRecurrenceDates([
            new \DateTime('2017-10-02 12:00:00', new DateTimeZone('UTC')),
            new \DateTime('2017-11-03 12:00:00', new DateTimeZone('America/New_York'))
        ]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RDATE:20171002T120000Z,20171103T160000Z', $iCalendarString);
    }

    public function testAddRecurrencePeriods() {
        // check for empty periods
        $this->todo->addRecurrencePeriods([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RDATE:', $iCalendarString);

        // check for periods that are not really Period objects
        $this->assertException(function() {
            $this->todo->addRecurrencePeriods(['Some text']);
        }, InvalidArgumentException::class, null, 'All values of periods must be instances of Period!');

        // check periods with end time and with duration
        $duration = (new Duration())->setHour(10)->setDay(10);

        $period1 = (new Period())
            ->setStart(new \DateTime('2015-11-25 12:54:22', new DateTimeZone('UTC')))
            ->setDuration($duration);
        $period2 = (new Period())
            ->setStart(new \DateTime('2016-02-10 10:00:00', new DateTimeZone('UTC')))
            ->setEnd(new \DateTime('2016-03-10 11:00:00', new DateTimeZone('UTC')));

        $this->todo->addRecurrencePeriods([$period1, $period2]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("RDATE;VALUE=PERIOD:20151125T125422Z/P10DT10H,20160210T100000Z/20160310T1100\r\n 00Z", $iCalendarString);
    }
    
    public function testSetRecurrenceRule() {
        $this->assertException(function() {
            $this->todo->setRecurrenceRule('FREQ=DAILY;COUNT=10');
        }, PHPUnit_Framework_Error::class);

        $recurrenceRule = new RecurrenceRule();
        $recurrenceRule->setFrequency(RecurrenceFrequency::HOURLY);
        $recurrenceRule->setInterval(2);
        $recurrenceRule->setCount(10);
        
        $this->todo->setRecurrenceRule($recurrenceRule);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RRULE:FREQ=HOURLY;INTERVAL=2;COUNT=10', $iCalendarString);
    }

    public function testCreateDateProperty() {
        $this->assertException(function() {
            $this->invokeMethod($this->todo, 'createDateProperty', [
                'DTSTART', '2016-12-12 11:00:00', false
            ]);
        }, PHPUnit_Framework_Error::class);

        // check normal date time value
        $dateTimeProperty = $this->invokeMethod($this->todo, 'createDateProperty', [
            'DTSTART', new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')), false
        ]);

        $this->assertTrue($dateTimeProperty instanceof Property);
        $this->assertTrue($dateTimeProperty instanceof DateTime);
        $this->assertEquals('20161212T110000Z', $dateTimeProperty->getValue());

        // check floating date time value
        $dateTimeProperty = $this->invokeMethod($this->todo, 'createDateProperty', [
            'DTSTART', new \DateTime('2016-12-12 11:00:00'), true
        ]);
        $this->assertTrue($dateTimeProperty instanceof Property);
        $this->assertTrue($dateTimeProperty instanceof DateTime);
        $this->assertEquals('20161212T110000', $dateTimeProperty->getValue());

        // check date only value
        $dateTimeProperty = $this->invokeMethod($this->todo, 'createDateProperty', [
            'DTSTART', new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')), false, ['VALUE' => 'DATE']
        ]);

        $this->assertTrue($dateTimeProperty instanceof Property);
        $this->assertTrue($dateTimeProperty instanceof DateTime);
        $this->assertEquals('20161212', $dateTimeProperty->getValue());
    }

    public function testCheckTimeZone() {
        $this->assertException(function() {
            $this->invokeMethod($this->todo, 'checkTimeZone', [
                '2016-12-12 11:00:00'
            ]);
        }, PHPUnit_Framework_Error::class);

        $timeZoneAdded = $this->invokeMethod($this->todo, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC'))
        ]);

        $this->assertEquals(false, $timeZoneAdded);

        $timeZoneAdded = $this->invokeMethod($this->todo, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin'))
        ]);

        $this->assertEquals(true, $timeZoneAdded);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("BEGIN:VTIMEZONE\r\nTZID:Europe/Berlin", $iCalendarString);
    }

    public function testHasTimeZone() {
        $this->assertFalse($this->invokeMethod($this->todo, 'hasTimeZone', ['UTC']));
        $this->assertFalse($this->invokeMethod($this->todo, 'hasTimeZone', ['Europe/Berlin']));

        $this->invokeMethod($this->todo, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin'))
        ]);
        $this->invokeMethod($this->todo, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC'))
        ]);

        // UTC won't be added
        $this->assertFalse($this->invokeMethod($this->todo, 'hasTimeZone', ['UTC']));
        $this->assertTrue($this->invokeMethod($this->todo, 'hasTimeZone', ['Europe/Berlin']));
    }

    public function testAddTimeZone() {
        $this->assertFalse($this->invokeMethod($this->todo, 'hasTimeZone', ['Europe/Berlin']));

        $this->invokeMethod($this->todo, 'addTimeZone', ['Europe/Berlin']);

        $this->assertTrue($this->invokeMethod($this->todo, 'hasTimeZone', ['Europe/Berlin']));
    }
}