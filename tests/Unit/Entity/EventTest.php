<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Entity\Event;
use Brainformatik\CalDAV\Enum\EventStatus;
use Brainformatik\CalDAV\Enum\EventTransparency;
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
use Sabre\VObject\Component\VEvent;
use Sabre\VObject\Property;
use Sabre\VObject\Property\ICalendar\DateTime;

class EventTest extends BaseTestCase {

    /**
     * @var VCalendar
     */
    protected $calendar;

    /**
     * @var Event
     */
    protected $event;

    public function setUp() {
        $this->calendar = new VCalendar();
        $this->event = new Event($this->calendar);
    }

    /*
     * The following tests are only for To-do
     */

    public function testConstruct() {
        $this->assertException(function() {
            new Event('Calendar');
        }, TypeError::class);

        $event = new Event($this->calendar);

        $vEvent = $this->invokeProperty($event, 'instance');

        $this->assertTrue($vEvent instanceof VEvent);

        $instanceType = $this->invokeProperty($event, 'type');

        $this->assertEquals('Event', $instanceType);
    }

    public function testSetDateEnd() {
        $this->assertException(function() {
            $this->event->setDateEnd('2016-12-12 11:00:00');
        }, TypeError::class);

        // test date time with time zone
        $this->event->setDateEnd(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTEND;TZID=Europe/Berlin:20161212T110000\r\n", $iCalendarString);

        // test if automatic embedding of time zone works correctly
        $this->assertContains("BEGIN:VTIMEZONE\r\nTZID:Europe/Berlin", $iCalendarString);

        // test floating time
        $this->event->setDateEnd(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), true);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTEND:20161212T110000\r\n", $iCalendarString);

        // test utc time zone
        $this->event->setDateEnd(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTEND:20161212T110000Z\r\n", $iCalendarString);

        // test date without time
        $this->event->setDateEnd(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), false, ['VALUE' => 'DATE']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTEND;VALUE=DATE:20161212\r\n", $iCalendarString);
    }

    public function testSetTransparency() {
        $this->assertException(function() {
            $this->event->setTransparency('UnknownTransparency');
        }, InvalidArgumentException::class, null, 'This transparency is not allowed for current entity!');

        $this->event->setTransparency(EventTransparency::TRANSPARENT);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("TRANSP:TRANSPARENT\r\n", $iCalendarString);
    }

    public function testIsStatusValid() {
        $this->assertTrue($this->invokeMethod($this->event, 'isStatusValid', [EventStatus::CONFIRMED]));
        $this->assertFalse($this->invokeMethod($this->event, 'isStatusValid', [TodoStatus::IN_PROCESS]));
    }

    /*
     * The following tests are for AbstractEntity methods
     */

    public function testSetUid() {
        $this->event->setUid('123456');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('UID:123456', $iCalendarString);
    }

    public function testSetSummary() {
        $this->event->setSummary('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('SUMMARY:', $iCalendarString);

        $this->event->setSummary('My first event');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('SUMMARY:My first event', $iCalendarString);
    }

    public function testSetDescription() {
        $this->event->setDescription('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('DESCRIPTION:', $iCalendarString);

        $this->event->setDescription('Description for my first event');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('DESCRIPTION:Description for my first event', $iCalendarString);
    }

    public function testSetComment() {
        $this->event->setComment('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('COMMENT:', $iCalendarString);

        $this->event->setComment('Comment for my first event');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('COMMENT:Comment for my first event', $iCalendarString);
    }

    public function testAddAttachment() {
        $this->event->addAttachment('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('ATTACH:', $iCalendarString);

        $this->event->addAttachment('http://domain.tdn/myImage.gif');
        $this->event->addAttachment('http://domain.tdn/myImage.png');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('ATTACH:http://domain.tdn/myImage.gif', $iCalendarString);
        $this->assertContains('ATTACH:http://domain.tdn/myImage.png', $iCalendarString);
    }

    public function testAddCategories() {
        $this->event->addCategories([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('CATEGORIES:', $iCalendarString);

        $this->event->addCategories(['FIRST', 'SECOND', 'THIRD']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('CATEGORIES:FIRST,SECOND,THIRD', $iCalendarString);
    }

    public function testSetClass() {
        $this->event->setClass('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('CLASS:', $iCalendarString);

        $this->event->setClass('PRIVATE');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('CLASS:PRIVATE', $iCalendarString);
    }

    public function testSetGeo() {
        $this->assertException(function() {
            $this->event->setGeo([5]);
        }, InvalidArgumentException::class, null, 'The array should contain two numbers!');
        $this->assertException(function() {
            $this->event->setGeo([5, false]);
        }, InvalidArgumentException::class, null, 'The array should contain two numbers!');
        $this->assertException(function() {
            $this->event->setGeo([4, 'Some text']);
        }, InvalidArgumentException::class, null, 'The array should contain two numbers!');

        $this->event->setGeo([20.25, 45.28]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('GEO:20.25;45.28', $iCalendarString);
    }

    public function testSetLocation() {
        $this->event->setLocation('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('LOCATION:', $iCalendarString);

        $this->event->setLocation('Meeting room');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('LOCATION:Meeting room', $iCalendarString);
    }

    public function testSetPriority() {
        $this->assertException(function() {
            $this->event->setPriority('Some text');
        }, InvalidArgumentException::class, null, 'Priority should be an integer value!');
        $this->assertException(function() {
            $this->event->setPriority(false);
        }, InvalidArgumentException::class, null, 'Priority should be an integer value!');
        $this->assertException(function() {
            $this->event->setPriority(-1);
        }, OutOfRangeException::class, null, 'Priority should be between 0 and 9!');
        $this->assertException(function() {
            $this->event->setPriority(10);
        }, OutOfRangeException::class, null, 'Priority should be between 0 and 9!');

        $this->event->setPriority(4);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('PRIORITY:4', $iCalendarString);
    }

    public function testAddResources() {
        $this->event->addResources([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RESOURCES:', $iCalendarString);

        $this->event->addResources(['FIRST', 'SECOND', 'THIRD']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RESOURCES:FIRST,SECOND,THIRD', $iCalendarString);
    }

    public function testSetStatus() {
        $this->assertException(function() {
            $this->event->setStatus('UnknownStatus');
        }, InvalidArgumentException::class, null, 'This status is not allowed for current entity!');
        $this->assertException(function() {
            $this->event->setStatus(TodoStatus::IN_PROCESS);
        }, InvalidArgumentException::class, null, 'This status is not allowed for current entity!');

        $this->event->setStatus(EventStatus::CONFIRMED);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('STATUS:CONFIRMED', $iCalendarString);
    }

    public function testSetDateStart() {
        $this->assertException(function() {
            $this->event->setDateStart('2016-12-12 11:00:00');
        }, TypeError::class);

        // test date time with time zone
        $this->event->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART;TZID=Europe/Berlin:20161212T110000\r\n", $iCalendarString);

        // test if automatic embedding of time zone works correctly
        $this->assertContains("BEGIN:VTIMEZONE\r\nTZID:Europe/Berlin", $iCalendarString);

        // test floating time
        $this->event->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), true);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART:20161212T110000\r\n", $iCalendarString);

        // test utc time zone
        $this->event->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART:20161212T110000Z\r\n", $iCalendarString);

        // test date without time
        $this->event->setDateStart(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')), false, ['VALUE' => 'DATE']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DTSTART;VALUE=DATE:20161212\r\n", $iCalendarString);
    }

    public function testSetDuration() {
        $this->assertException(function() {
            $this->event->setDuration('P4W');
        }, TypeError::class);

        $duration = new Duration();
        $duration->setWeek(5);

        $this->event->setDuration($duration);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DURATION:P5W\r\n", $iCalendarString);
    }

    public function testSetRelatedTo() {
        $this->event->setRelatedTo('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RELATED-TO:', $iCalendarString);

        $this->event->setRelatedTo('56789');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RELATED-TO:56789', $iCalendarString);

        $this->event->setRelatedTo('123456', ['RELTYPE' => 'CHILD']);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RELATED-TO;RELTYPE=CHILD:123456', $iCalendarString);
    }

    public function testSetUrl() {
        $this->event->setUrl('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('URL:', $iCalendarString);

        $this->event->setUrl('http://domain.tdn');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('URL;VALUE=URI:http://domain.tdn', $iCalendarString);
    }

    public function testAddAttendee() {
        $this->assertException(function() {
            $this->event->addAttendee('John Doe');
        }, TypeError::class);

        $this->assertException(function() {
            $wrongAttendeeType = new Attendee('address@domain.tdn', 'Todo');
            $this->event->addAttendee($wrongAttendeeType);
        }, InvalidArgumentException::class, null, 'Type of entity and target entity type of attendee must match!');

        $attendee = new Attendee('address@domain.tdn');

        $this->event->addAttendee($attendee);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('ATTENDEE:mailto:address@domain.tdn', $iCalendarString);
    }

    public function testSetContact() {
        $this->assertException(function() {
            $this->event->setContact('John Doe');
        }, TypeError::class);

        $contact = new Contact('John Doe');

        $this->event->setContact($contact);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('CONTACT:John Doe', $iCalendarString);
    }

    public function testSetOrganizer() {
        $this->assertException(function() {
            $this->event->setOrganizer('John Doe');
        }, TypeError::class);

        $organizer = new Organizer('address@domain.tdn');

        $this->event->setOrganizer($organizer);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('ORGANIZER:mailto:address@domain.tdn', $iCalendarString);
    }

    public function testSetDateCreated() {
        $this->assertException(function() {
            $this->event->setDateCreated('2016-12-12 11:00:00');
        }, TypeError::class);

        $this->assertException(function() {
            $this->event->setDateCreated(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));
        }, InvalidArgumentException::class, null, 'The value must use UTC as time zone!');

        $this->event->setDateCreated(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("CREATED:20161212T110000Z\r\n", $iCalendarString);
    }

    public function testSetDateLastModified() {
        $this->assertException(function() {
            $this->event->setDateLastModified('2016-12-12 11:00:00');
        }, TypeError::class);

        $this->assertException(function() {
            $this->event->setDateLastModified(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin')));
        }, InvalidArgumentException::class, null, 'The value must use UTC as time zone!');

        $this->event->setDateLastModified(new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("MODIFIED:20161212T110000Z\r\n", $iCalendarString);
    }

    public function testSetSequence() {
        $this->assertException(function() {
            $this->event->setSequence('Some text');
        }, InvalidArgumentException::class, null, 'Sequence should be an integer value!');
        $this->assertException(function() {
            $this->event->setSequence(false);
        }, InvalidArgumentException::class, null, 'Sequence should be an integer value!');

        $this->event->setSequence(1);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("SEQUENCE:1\r\n", $iCalendarString);
    }

    public function testSetRecurrenceId() {
        $this->event->setRecurrenceId('');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RECURRENCE-ID:', $iCalendarString);

        $this->event->setRecurrenceId('123456');

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RECURRENCE-ID:123456', $iCalendarString);
    }

    public function testAddExceptionDates() {
        $this->event->addExceptionDates([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('EXDATE:', $iCalendarString);

        $this->event->addExceptionDates([
            new \DateTime('2015-12-12 12:00:00', new DateTimeZone('America/New_York')),
            new \DateTime('2015-12-14 12:00:00', new DateTimeZone('Europe/Berlin'))
        ]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('EXDATE;TZID=America/New_York:20151212T120000,20151214T060000', $iCalendarString);
    }

    public function testAddRecurrenceDates() {
        $this->assertException(function() {
            $this->event->addRecurrenceDates([], ['VALUE' => 'PERIOD']);
        }, InvalidArgumentException::class, null, 'You have to use addRecurrencePeriods to set periods!');

        $this->event->addRecurrenceDates([
            new \DateTime('2017-10-02 12:00:00', new DateTimeZone('UTC')),
            new \DateTime('2017-11-03 12:00:00', new DateTimeZone('America/New_York'))
        ]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RDATE:20171002T120000Z,20171103T160000Z', $iCalendarString);
    }

    public function testAddRecurrencePeriods() {
        // check for empty periods
        $this->event->addRecurrencePeriods([]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('RDATE:', $iCalendarString);

        // check for periods that are not really Period objects
        $this->assertException(function() {
            $this->event->addRecurrencePeriods(['Some text']);
        }, InvalidArgumentException::class, null, 'All values of periods must be instances of Period!');

        // check periods with end time and with duration
        $duration = (new Duration())->setHour(10)->setDay(10);

        $period1 = (new Period())
            ->setStart(new \DateTime('2015-11-25 12:54:22', new DateTimeZone('UTC')))
            ->setDuration($duration);
        $period2 = (new Period())
            ->setStart(new \DateTime('2016-02-10 10:00:00', new DateTimeZone('UTC')))
            ->setEnd(new \DateTime('2016-03-10 11:00:00', new DateTimeZone('UTC')));

        $this->event->addRecurrencePeriods([$period1, $period2]);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("RDATE;VALUE=PERIOD:20151125T125422Z/P10DT10H,20160210T100000Z/20160310T1100\r\n 00Z", $iCalendarString);
    }
    
    public function testSetRecurrenceRule() {
        $this->assertException(function() {
            $this->event->setRecurrenceRule('FREQ=DAILY;COUNT=10');
        }, TypeError::class);

        $recurrenceRule = new RecurrenceRule();
        $recurrenceRule->setFrequency(RecurrenceFrequency::HOURLY);
        $recurrenceRule->setInterval(2);
        $recurrenceRule->setCount(10);
        
        $this->event->setRecurrenceRule($recurrenceRule);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('RRULE:FREQ=HOURLY;INTERVAL=2;COUNT=10', $iCalendarString);
    }

    public function testCreateDateProperty() {
        $this->assertException(function() {
            $this->invokeMethod($this->event, 'createDateProperty', [
                'DTSTART', '2016-12-12 11:00:00', false
            ]);
        }, TypeError::class);

        // check normal date time value
        $dateTimeProperty = $this->invokeMethod($this->event, 'createDateProperty', [
            'DTSTART', new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')), false
        ]);

        $this->assertTrue($dateTimeProperty instanceof Property);
        $this->assertTrue($dateTimeProperty instanceof DateTime);
        $this->assertEquals('20161212T110000Z', $dateTimeProperty->getValue());

        // check floating date time value
        $dateTimeProperty = $this->invokeMethod($this->event, 'createDateProperty', [
            'DTSTART', new \DateTime('2016-12-12 11:00:00'), true
        ]);
        $this->assertTrue($dateTimeProperty instanceof Property);
        $this->assertTrue($dateTimeProperty instanceof DateTime);
        $this->assertEquals('20161212T110000', $dateTimeProperty->getValue());

        // check date only value
        $dateTimeProperty = $this->invokeMethod($this->event, 'createDateProperty', [
            'DTSTART', new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')), false, ['VALUE' => 'DATE']
        ]);

        $this->assertTrue($dateTimeProperty instanceof Property);
        $this->assertTrue($dateTimeProperty instanceof DateTime);
        $this->assertEquals('20161212', $dateTimeProperty->getValue());
    }

    public function testCheckTimeZone() {
        $this->assertException(function() {
            $this->invokeMethod($this->event, 'checkTimeZone', [
                '2016-12-12 11:00:00'
            ]);
        }, TypeError::class);

        $timeZoneAdded = $this->invokeMethod($this->event, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC'))
        ]);

        $this->assertEquals(false, $timeZoneAdded);

        $timeZoneAdded = $this->invokeMethod($this->event, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin'))
        ]);

        $this->assertEquals(true, $timeZoneAdded);

        $iCalendarString = $this->calendar->serialize();

        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("BEGIN:VTIMEZONE\r\nTZID:Europe/Berlin", $iCalendarString);
    }

    public function testHasTimeZone() {
        $this->assertFalse($this->invokeMethod($this->event, 'hasTimeZone', ['UTC']));
        $this->assertFalse($this->invokeMethod($this->event, 'hasTimeZone', ['Europe/Berlin']));

        $this->invokeMethod($this->event, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('Europe/Berlin'))
        ]);
        $this->invokeMethod($this->event, 'checkTimeZone', [
            new \DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC'))
        ]);

        // UTC won't be added
        $this->assertFalse($this->invokeMethod($this->event, 'hasTimeZone', ['UTC']));
        $this->assertTrue($this->invokeMethod($this->event, 'hasTimeZone', ['Europe/Berlin']));
    }

    public function testAddTimeZone() {
        $this->assertFalse($this->invokeMethod($this->event, 'hasTimeZone', ['Europe/Berlin']));

        $this->invokeMethod($this->event, 'addTimeZone', ['Europe/Berlin']);

        $this->assertTrue($this->invokeMethod($this->event, 'hasTimeZone', ['Europe/Berlin']));
    }
}