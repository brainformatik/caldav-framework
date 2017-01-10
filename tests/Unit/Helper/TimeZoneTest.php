<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Helper\TimeZone;
use Sabre\VObject\Component\VTimeZone;

class TimeZoneTest extends BaseTestCase {

    public function testGetTimeZone() {
        $this->assertException(function() {
            TimeZone::getTimeZone('Europe/My_Country');
        }, InvalidArgumentException::class, null, 'No time zone file found for the given ID!');

        $timeZone = TimeZone::getTimeZone('Europe/Berlin');

        $this->assertTrue($timeZone instanceof VTimeZone);
    }
}