<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class EventStatus
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.11
 *
 * @package Brainformatik\CalDAV\Enum
 */
class EventStatus extends AbstractEnum {
    const TENTATIVE = 'TENTATIVE'; // indicates event is tentative
    const CONFIRMED = 'CONFIRMED'; // indicates event is definite
    const CANCELLED = 'CANCELLED'; // indicates event was cancelled
}