<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brainformatik\CalDAV\Helper\BaseTestCase;
use Brainformatik\CalDAV\Type\Duration;
use Brainformatik\CalDAV\Type\Period;

class PeriodTest extends BaseTestCase {

    /**
     * @var Period
     */
    protected $period;

    public function setUp() {
        $this->period = new Period();
    }

    public function testSetStart() {
        $this->assertException(function() {
            $this->period->setStart('2016-12-12 11:00:00');
        }, PHPUnit_Framework_Error::class);

        $this->assertException(function () {
            $newYorkTime = new DateTime('2016-12-12 11:00:00', new DateTimeZone('America/New_York'));
            $this->period->setStart($newYorkTime);
        }, InvalidArgumentException::class, null, 'Time zone of start must be UTC!');

        $this->period->setStart(new DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));
    }

    public function testSetEnd() {
        $this->assertException(function() {
            $this->period->setEnd('2016-12-12 11:00:00');
        }, PHPUnit_Framework_Error::class);

        $this->assertException(function () {
            $newYorkTime = new DateTime('2016-12-12 11:00:00', new DateTimeZone('America/New_York'));
            $this->period->setEnd($newYorkTime);
        }, InvalidArgumentException::class, null, 'Time zone of end must be UTC!');

        $this->period->setEnd(new DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));
    }

    public function testSetDuration() {
        $this->assertException(function() {
            $this->period->setDuration('P4W');
        }, PHPUnit_Framework_Error::class);

        $duration = new Duration();
        $this->period->setDuration($duration);
    }

    public function testToString() {
        $periodWithEnd = new Period();

        $this->assertException(function() use($periodWithEnd) {
            $periodWithEnd->toString();
        }, UnexpectedValueException::class, null, 'No start date time is set!');

        $periodWithEnd->setStart(new DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $this->assertException(function() use($periodWithEnd) {
            $periodWithEnd->toString();
        }, UnexpectedValueException::class, null, 'Either end date time or duration must be set!');

        $periodWithEnd->setEnd(new DateTime('2016-12-15 11:00:00', new DateTimeZone('UTC')));

        $this->assertEquals('20161212T110000Z/20161215T110000Z', $periodWithEnd->toString());

        $duration = new Duration();
        $duration->setDay(10);

        $periodWithEnd->setDuration($duration);

        // because end will be preferred, period stays the same even if duration is set
        $this->assertEquals('20161212T110000Z/20161215T110000Z', $periodWithEnd->toString());

        $periodWithDuration = new Period();

        $this->assertException(function() use($periodWithDuration) {
            $periodWithDuration->toString();
        }, UnexpectedValueException::class, null, 'No start date time is set!');

        $periodWithDuration->setStart(new DateTime('2016-12-12 11:00:00', new DateTimeZone('UTC')));

        $this->assertException(function() use($periodWithDuration) {
            $periodWithDuration->toString();
        }, UnexpectedValueException::class, null, 'Either end date time or duration must be set!');

        $duration = new Duration();
        $duration->setDay(10);

        $periodWithDuration->setDuration($duration);

        $this->assertEquals('20161212T110000Z/P10D', $periodWithDuration->toString());
    }
}
