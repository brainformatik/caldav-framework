<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Enum\CalendarUserType;
use Brainformatik\CalDAV\Enum\ParticipantRole;
use Brainformatik\CalDAV\Enum\ParticipantStatus;
use Brainformatik\CalDAV\Enum\ParticipantStatusTodo;
use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\Attendee;

class AttendeeTest extends BaseTestCase {

    /**
     * Testing properties for attendee that will be inserted into an event
     *
     * @var Attendee
     */
    protected $eventAttendee;

    /**
     * Testing properties for attendee that will be inserted into a to-do
     *
     * @var Attendee
     */
    protected $todoAttendee;

    public function setUp() {
        $this->eventAttendee = new Attendee('address@some-domain.tdn', 'Event');
        $this->todoAttendee = new Attendee('address@some-domain.tdn', 'Todo');
    }

    public function testConstruct() {
        $this->assertException(function() {
            new Attendee('');
        }, InvalidArgumentException::class, null, 'Mail address cannot be empty!');

        $this->assertException(function() {
            new Attendee('address@some-domain.tdn', 'Unknown');
        }, InvalidArgumentException::class, null, 'Target entity type must be "Event" or "Todo"!');
    }

    public function testGetEntityType() {
        $attendee = new Attendee('address@some-domain.tdn', 'Event');

        $this->assertEquals('Event', $attendee->getEntityType());

        $attendee = new Attendee('address@some-domain.tdn', 'Todo');

        $this->assertEquals('Todo', $attendee->getEntityType());
    }

    public function testGetAddress() {
        $attendee = new Attendee('address@some-domain.tdn', 'Event');

        $this->assertEquals('mailto:address@some-domain.tdn', $attendee->getAddress());
    }

    public function testGetParameters() {
        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertCount(0, $parameters);
    }

    public function testSetName() {
        $this->eventAttendee->setName('');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('CN', $parameters);

        $this->eventAttendee->setName('John Doe');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('CN', $parameters);
        $this->assertEquals('John Doe', $parameters['CN']);

        $this->todoAttendee->setName('');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('CN', $parameters);

        $this->todoAttendee->setName('John Doe');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('CN', $parameters);
        $this->assertEquals('John Doe', $parameters['CN']);
    }

    public function testSetRole() {
        $this->assertException(function() {
            $this->eventAttendee->setRole('NotExistingRole');
        }, InvalidArgumentException::class, null, 'Role not in enum ParticipantRole!');
        $this->assertException(function() {
            $this->todoAttendee->setRole('NotExistingRole');
        }, InvalidArgumentException::class, null, 'Role not in enum ParticipantRole!');

        $this->eventAttendee->setRole(ParticipantRole::CHAIR);

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('ROLE', $parameters);
        $this->assertEquals(ParticipantRole::CHAIR, $parameters['ROLE']);

        $this->todoAttendee->setRole(ParticipantRole::REQ_PARTICIPANT);

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('ROLE', $parameters);
        $this->assertEquals(ParticipantRole::REQ_PARTICIPANT, $parameters['ROLE']);
    }

    public function testSetParticipantStatus() {
        $this->assertException(function() {
            $this->eventAttendee->setParticipantStatus('NotExistingStatus');
        }, InvalidArgumentException::class, null, 'Status not in enum ParticipantStatus!');
        $this->assertException(function() {
            $this->todoAttendee->setParticipantStatus('NotExistingStatus');
        }, InvalidArgumentException::class, null, 'Status not in enum ParticipantStatusTodo!');

        $this->eventAttendee->setParticipantStatus(ParticipantStatus::ACCEPTED);

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('PARTSTAT', $parameters);
        $this->assertEquals(ParticipantStatus::ACCEPTED, $parameters['PARTSTAT']);

        $this->todoAttendee->setParticipantStatus(ParticipantStatusTodo::IN_PROCESS);

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('PARTSTAT', $parameters);
        $this->assertEquals(ParticipantStatusTodo::IN_PROCESS, $parameters['PARTSTAT']);
    }

    public function testSetUserType() {
        $this->assertException(function() {
            $this->eventAttendee->setUserType('NotExistingUserType');
        }, InvalidArgumentException::class, null, 'Value not in enum CalendarUserType!');
        $this->assertException(function() {
            $this->todoAttendee->setUserType('NotExistingUserType');
        }, InvalidArgumentException::class, null, 'Value not in enum CalendarUserType!');

        $this->eventAttendee->setUserType(CalendarUserType::GROUP);

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('CUTYPE', $parameters);
        $this->assertEquals(CalendarUserType::GROUP, $parameters['CUTYPE']);

        $this->todoAttendee->setUserType(CalendarUserType::INDIVIDUAL);

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('CUTYPE', $parameters);
        $this->assertEquals(CalendarUserType::INDIVIDUAL, $parameters['CUTYPE']);
    }

    public function testAddGroupMember() {
        $this->eventAttendee->addGroupMember('');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('MEMBER', $parameters);

        $this->eventAttendee->addGroupMember('member1@group.tdn');
        $this->eventAttendee->addGroupMember('member2@group.tdn');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('MEMBER', $parameters);
        $this->assertCount(2, $parameters['MEMBER']);
        $this->assertContains('mailto:member1@group.tdn', $parameters['MEMBER']);
        $this->assertContains('mailto:member2@group.tdn', $parameters['MEMBER']);

        $this->todoAttendee->addGroupMember('');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('MEMBER', $parameters);

        $this->todoAttendee->addGroupMember('member1@group.tdn');
        $this->todoAttendee->addGroupMember('member2@group.tdn');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('MEMBER', $parameters);
        $this->assertCount(2, $parameters['MEMBER']);
        $this->assertContains('mailto:member1@group.tdn', $parameters['MEMBER']);
        $this->assertContains('mailto:member2@group.tdn', $parameters['MEMBER']);
    }

    public function testAddDelegatedTo() {
        $this->eventAttendee->addDelegatedTo('');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('DELEGATED-TO', $parameters);

        $this->eventAttendee->addDelegatedTo('delegated1@group.tdn');
        $this->eventAttendee->addDelegatedTo('delegated2@group.tdn');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('DELEGATED-TO', $parameters);
        $this->assertCount(2, $parameters['DELEGATED-TO']);
        $this->assertContains('mailto:delegated1@group.tdn', $parameters['DELEGATED-TO']);
        $this->assertContains('mailto:delegated2@group.tdn', $parameters['DELEGATED-TO']);

        $this->todoAttendee->addDelegatedTo('');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('DELEGATED-TO', $parameters);

        $this->todoAttendee->addDelegatedTo('delegated1@group.tdn');
        $this->todoAttendee->addDelegatedTo('delegated2@group.tdn');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('DELEGATED-TO', $parameters);
        $this->assertCount(2, $parameters['DELEGATED-TO']);
        $this->assertContains('mailto:delegated1@group.tdn', $parameters['DELEGATED-TO']);
        $this->assertContains('mailto:delegated2@group.tdn', $parameters['DELEGATED-TO']);
    }

    public function testAddDelegatedFrom() {
        $this->eventAttendee->addDelegatedFrom('');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('DELEGATED-FROM', $parameters);

        $this->eventAttendee->addDelegatedFrom('delegated1@group.tdn');
        $this->eventAttendee->addDelegatedFrom('delegated2@group.tdn');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('DELEGATED-FROM', $parameters);
        $this->assertCount(2, $parameters['DELEGATED-FROM']);
        $this->assertContains('mailto:delegated1@group.tdn', $parameters['DELEGATED-FROM']);
        $this->assertContains('mailto:delegated2@group.tdn', $parameters['DELEGATED-FROM']);

        $this->todoAttendee->addDelegatedFrom('');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('DELEGATED-FROM', $parameters);

        $this->todoAttendee->addDelegatedFrom('delegated1@group.tdn');
        $this->todoAttendee->addDelegatedFrom('delegated2@group.tdn');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('DELEGATED-FROM', $parameters);
        $this->assertCount(2, $parameters['DELEGATED-FROM']);
        $this->assertContains('mailto:delegated1@group.tdn', $parameters['DELEGATED-FROM']);
        $this->assertContains('mailto:delegated2@group.tdn', $parameters['DELEGATED-FROM']);
    }

    public function testSetSentBy() {
        $this->eventAttendee->setSentBy('');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('SENT-BY', $parameters);

        $this->eventAttendee->setSentBy('sender@domain.tdn');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('SENT-BY', $parameters);
        $this->assertEquals('mailto:sender@domain.tdn', $parameters['SENT-BY']);

        $this->todoAttendee->setSentBy('');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('SENT-BY', $parameters);

        $this->todoAttendee->setSentBy('sender@domain.tdn');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('SENT-BY', $parameters);
        $this->assertEquals('mailto:sender@domain.tdn', $parameters['SENT-BY']);
    }

    public function testSetDirectory() {
        $this->eventAttendee->setDirectory('');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('DIR', $parameters);

        $this->eventAttendee->setDirectory('http://domain.tdn');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('DIR', $parameters);
        $this->assertEquals('http://domain.tdn', $parameters['DIR']);

        $this->todoAttendee->setDirectory('');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('DIR', $parameters);

        $this->todoAttendee->setDirectory('http://domain.tdn');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('DIR', $parameters);
        $this->assertEquals('http://domain.tdn', $parameters['DIR']);
    }

    public function testSetLanguage() {
        $this->eventAttendee->setLanguage('');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('LANGUAGE', $parameters);

        $this->eventAttendee->setLanguage('de');

        $parameters = $this->eventAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('LANGUAGE', $parameters);
        $this->assertEquals('de', $parameters['LANGUAGE']);

        $this->todoAttendee->setLanguage('');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('LANGUAGE', $parameters);

        $this->todoAttendee->setLanguage('de');

        $parameters = $this->todoAttendee->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('LANGUAGE', $parameters);
        $this->assertEquals('de', $parameters['LANGUAGE']);
    }
}