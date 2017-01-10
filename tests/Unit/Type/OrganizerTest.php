<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\Organizer;

class OrganizerTest extends BaseTestCase {

    /**
     * @var Organizer
     */
    protected $organizer;

    public function setUp() {
        $this->organizer = new Organizer('address@some-domain.tdn');
    }

    public function testGetAddress() {
        $organizer = new Organizer('address@some-domain.tdn');

        $this->assertEquals('mailto:address@some-domain.tdn', $organizer->getAddress());
    }

    public function testGetParameters() {
        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertCount(0, $parameters);
    }

    public function testSetName() {
        $this->organizer->setName('');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('CN', $parameters);

        $this->organizer->setName('John Doe');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('CN', $parameters);
        $this->assertEquals('John Doe', $parameters['CN']);
    }

    public function testSetSentBy() {
        $this->organizer->setSentBy('');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('SENT-BY', $parameters);

        $this->organizer->setSentBy('sender@domain.tdn');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('SENT-BY', $parameters);
        $this->assertEquals('mailto:sender@domain.tdn', $parameters['SENT-BY']);
    }

    public function testSetDirectory() {
        $this->organizer->setDirectory('');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('DIR', $parameters);

        $this->organizer->setDirectory('http://domain.tdn');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('DIR', $parameters);
        $this->assertEquals('http://domain.tdn', $parameters['DIR']);
    }

    public function testSetLanguage() {
        $this->organizer->setLanguage('');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('LANGUAGE', $parameters);

        $this->organizer->setLanguage('de');

        $parameters = $this->organizer->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('LANGUAGE', $parameters);
        $this->assertEquals('de', $parameters['LANGUAGE']);
    }
}