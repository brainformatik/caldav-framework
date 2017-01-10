<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class ParticipantRole
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.2.16
 *
 * @package Brainformatik\CalDAV\Enum
 */
class ParticipantRole extends AbstractEnum {
    const CHAIR =           'CHAIR';           // indicates chair of the calendar entity
    const REQ_PARTICIPANT = 'REQ-PARTICIPANT'; // indicates a participant whose participation is required
    const OPT_PARTICIPANT = 'OPT-PARTICIPANT'; // indicates a participant whose participation is optional
    const NON_PARTICIPANT = 'NON-PARTICIPANT'; // indicates a participant who is copied for information purposes only
}