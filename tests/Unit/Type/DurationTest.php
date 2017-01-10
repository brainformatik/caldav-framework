<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\Duration;

class DurationTest extends BaseTestCase {

    /**
     * @var Duration
     */
    protected $duration;

    public function setUp() {
        $this->duration = new Duration();
    }

    public function testToString() {
        $this->assertException(function() {
            $this->duration->toString();
        }, UnexpectedValueException::class, null, 'At least one duration value must be set!');

        $this->duration->setWeek(1);

        $this->assertNotEmpty($this->duration->toString());
    }

    public function testSetDay() {
        $this->assertException(function() {
            $this->duration->setDay('some text');
        }, InvalidArgumentException::class, null, 'Value for day must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setDay(false);
        }, InvalidArgumentException::class, null, 'Value for day must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setDay(0);
        }, InvalidArgumentException::class, null, 'Value for day must be integer greater than 0!');

        $this->duration->setDay(10);

        $this->assertEquals('P10D', $this->duration->toString());
    }

    public function testSetHour() {
        $this->assertException(function() {
            $this->duration->setHour('some text');
        }, InvalidArgumentException::class, null, 'Value for hour must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setHour(false);
        }, InvalidArgumentException::class, null, 'Value for hour must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setHour(0);
        }, InvalidArgumentException::class, null, 'Value for hour must be integer greater than 0!');

        $this->duration->setHour(10);

        $this->assertEquals('PT10H', $this->duration->toString());
    }

    public function testSetMinute() {
        $this->assertException(function() {
            $this->duration->setMinute('some text');
        }, InvalidArgumentException::class, null, 'Value for minute must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setMinute(false);
        }, InvalidArgumentException::class, null, 'Value for minute must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setMinute(0);
        }, InvalidArgumentException::class, null, 'Value for minute must be integer greater than 0!');

        $this->duration->setMinute(10);

        $this->assertEquals('PT10M', $this->duration->toString());
    }

    public function testSetSecond() {
        $this->assertException(function() {
            $this->duration->setSecond('some text');
        }, InvalidArgumentException::class, null, 'Value for second must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setSecond(false);
        }, InvalidArgumentException::class, null, 'Value for second must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setSecond(0);
        }, InvalidArgumentException::class, null, 'Value for second must be integer greater than 0!');

        $this->duration->setSecond(10);

        $this->assertEquals('PT10S', $this->duration->toString());
    }

    public function testSetWeek() {
        $this->assertException(function() {
            $this->duration->setWeek('some text');
        }, InvalidArgumentException::class, null, 'Value for week must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setWeek(false);
        }, InvalidArgumentException::class, null, 'Value for week must be integer greater than 0!');
        $this->assertException(function() {
            $this->duration->setWeek(0);
        }, InvalidArgumentException::class, null, 'Value for week must be integer greater than 0!');

        $this->duration->setWeek(10);

        $this->assertEquals('P10W', $this->duration->toString());
    }
}
