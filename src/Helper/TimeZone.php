<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Helper;

use Sabre\VObject\Component\VTimeZone;
use Sabre\VObject\Reader;

/**
 * Class TimeZone
 *
 * This class contains helper methods regarding time zones
 *
 * @package Brainformatik\CalDAV\Helper
 */
class TimeZone {

    const RESOURCE_PATH = __DIR__ . '/../../resource/zoneinfo/';

    /**
     * Returns the time zone object for given time zone id
     *
     * @param string $tzId
     *
     * @return VTimeZone
     *
     * @throws \InvalidArgumentException
     */
    public static function getTimeZone($tzId) {
        $icsPath = self::RESOURCE_PATH . $tzId . '.ics';

        if (!file_exists($icsPath)) {
            throw new \InvalidArgumentException('No time zone file found for the given ID!');
        }

        $calendar = Reader::read(
            file_get_contents($icsPath)
        );

        $timeZone = $calendar->select('VTIMEZONE')[0];

        if (false === $timeZone instanceof VTimeZone) {
            throw new \InvalidArgumentException('Time zone object could not be retrieved!');
        }

        $timeZoneName = $timeZone->getTimeZone()->getName();

        if ($timeZoneName !== $tzId) {
            throw new \InvalidArgumentException('The found time zone ID does not match the given ID!');
        }

        return $timeZone;
    }
}