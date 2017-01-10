<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class RecurrenceFrequency
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.3.10
 *
 * @package Brainformatik\CalDAV\Enum
 */
class RecurrenceFrequency extends AbstractEnum {
    const SECONDLY = 'SECONDLY';
    const MINUTELY = 'MINUTELY';
    const HOURLY   = 'HOURLY';
    const DAILY    = 'DAILY';
    const WEEKLY   = 'WEEKLY';
    const MONTHLY  = 'MONTHLY';
    const YEARLY   = 'YEARLY';
}