<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\Contact;

class ContactTest extends BaseTestCase {

    public function testGetAddress() {
        $contact = new Contact('John Doe, Mainstreet 1, Some town');

        $address = $contact->getAddress();

        $this->assertEquals('John Doe, Mainstreet 1, Some town', $address);
    }

    public function testSetLanguage() {
        $contact = new Contact('John Doe');

        $contact->setLanguage('');

        $parameters = $contact->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('LANGUAGE', $parameters);

        $contact->setLanguage('de');

        $parameters = $contact->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('LANGUAGE', $parameters);
        $this->assertEquals('de', $parameters['LANGUAGE']);
    }

    public function testSetAlternateRepresentation() {
        $contact = new Contact('John Doe');

        $contact->setAlternateRepresentation('');

        $parameters = $contact->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayNotHasKey('ALTREP', $parameters);

        $contact->setAlternateRepresentation('http://domain.tdn');

        $parameters = $contact->getParameters();

        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('ALTREP', $parameters);
        $this->assertEquals('http://domain.tdn', $parameters['ALTREP']);
    }
}
