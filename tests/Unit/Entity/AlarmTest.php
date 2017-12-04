<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Entity\Alarm;
use Brainformatik\CalDAV\Entity\Todo;
use Brainformatik\CalDAV\Enum\Action;
use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\Attendee;
use Brainformatik\CalDAV\Type\Duration;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Component\VTodo;
use Sabre\VObject\Component\VAlarm;
use Sabre\VObject\Property;
use Sabre\VObject\Property\ICalendar\DateTime;

class AlarmTest extends BaseTestCase {
    
    /**
     * @var VCalendar
     */
    protected $calendar;
    
    /**
     * @var Todo
     */
    protected $todo;
    
    /**
     * @var Alarm
     */
    protected $alarm;
    
    public function setUp() {
        $this->calendar = new VCalendar();
        $this->todo = new Todo($this->calendar);
        $this->alarm = $this->todo->addAlarm();
    }
    
    /*
     * The following tests are only for To-do
     */
    
    public function testConstruct() {
        $this->assertException(function () {
            new Alarm('Calendar', $this->todo);
        }, TypeError::class);
        
        $alarm = new Alarm($this->calendar, $this->todo);
        
        $vAlarm = $this->invokeProperty($alarm, 'instance');
        
        $this->assertTrue($vAlarm instanceof VAlarm);
    }
    
    public function testSetSummary() {
        $this->alarm->setSummary('');
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('SUMMARY:', $iCalendarString);
        
        $this->alarm->setSummary('My first event');
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('SUMMARY:My first event', $iCalendarString);
    }
    
    public function testSetDescription() {
        $this->alarm->setDescription('');
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertNotContains('DESCRIPTION:', $iCalendarString);
        
        $this->alarm->setDescription('Description for my first event');
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('DESCRIPTION:Description for my first event', $iCalendarString);
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
    
    public function testSetRepeatCount() {
        $this->assertException(function () {
            $this->alarm->setRepeatCount('Some text');
        }, InvalidArgumentException::class, null, 'repeat count should be an integer value!');
        $this->assertException(function () {
            $this->alarm->setRepeatCount(false);
        }, InvalidArgumentException::class, null, 'repeat count should be an integer value!');
        $this->assertException(function () {
            $this->alarm->setRepeatCount(-1);
        }, OutOfRangeException::class, null, 'repeat count should be greater than 0!');
        
        $this->alarm->setRepeatCount(4);
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('REPEAT:4', $iCalendarString);
    }
    
    public function testSetDuration() {
        $this->assertException(function () {
            $this->alarm->setDuration('P4W');
        }, TypeError::class);
        
        $duration = new Duration();
        $duration->setWeek(5);
        
        $this->alarm->setDuration($duration);
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("DURATION:P5W\r\n", $iCalendarString);
    }
    
    public function testAddAttendee() {
        $this->assertException(function () {
            $this->alarm->addAttendee('John Doe');
        }, TypeError::class);
        
        $attendee = new Attendee('address@domain.tdn', 'Todo');
        
        $this->alarm->addAttendee($attendee);
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains('ATTENDEE:mailto:address@domain.tdn', $iCalendarString);
    }
    
    public function testSetTrigger() {
        $this->assertException(function () {
            $this->alarm->setTrigger('P4W');
        }, TypeError::class);
        
        $duration = new Duration();
        $duration->setWeek(5);
        
        $this->alarm->setTrigger($duration);
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("TRIGGER:P5W\r\n", $iCalendarString);
    }
    
    public function testSetAction() {
        $this->assertException(function () {
            $this->alarm->setAction('UnknownFrequency');
        }, InvalidArgumentException::class, null, 'This action is not allowed for current entity!');
        
        $this->alarm->setAction(Action::DISPLAY);
        
        $iCalendarString = $this->calendar->serialize();
        
        $this->assertInternalType('string', $iCalendarString);
        $this->assertContains("ACTION:DISPLAY\r\n", $iCalendarString);
    }
    
}