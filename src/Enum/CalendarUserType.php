<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class CalendarUserType
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.2.3
 *
 * @package Brainformatik\CalDAV\Enum
 */
class CalendarUserType extends AbstractEnum {
    const INDIVIDUAL = 'INDIVIDUAL'; // an individual
    const GROUP =      'GROUP';      // a group of individuals
    const RESOURCE =   'RESOURCE';   // a physical resource
    const ROOM =       'ROOM';       // a room resource
    const UNKNOWN =    'UNKNOWN';    // otherwise not known
}