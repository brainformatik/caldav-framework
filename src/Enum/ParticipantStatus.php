<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class ParticipantStatus
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.2.12
 *
 * @package Brainformatik\CalDAV\Enum
 */
class ParticipantStatus extends AbstractEnum {
    const NEEDS_ACTION = 'NEEDS-ACTION';
    const ACCEPTED =     'ACCEPTED';
    const DECLINED =     'DECLINED';
    const TENTATIVE =    'TENTATIVE';
    const DELEGATED =    'DELEGATED';
}