<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class ParticipantStatusTodo
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.2.12
 *
 * @package Brainformatik\CalDAV\Enum
 */
class ParticipantStatusTodo extends ParticipantStatus {
    const COMPLETED = 'COMPLETED';
    const IN_PROCESS = 'IN-PROCESS';
}