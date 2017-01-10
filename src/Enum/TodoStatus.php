<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class TodoStatus
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.11
 *
 * @package Brainformatik\CalDAV\Enum
 */
class TodoStatus extends AbstractEnum {
    const NEEDS_ACTION = 'NEEDS-ACTION'; // indicates to-do needs action
    const COMPLETED    = 'COMPLETED';    // indicates to-do completed
    const IN_PROCESS   = 'IN-PROCESS';   // indicates to-do in process of
    const CANCELLED    = 'CANCELLED';    // indicates to-do was cancelled
}