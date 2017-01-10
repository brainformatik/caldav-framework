<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class EventTransparency
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.7
 *
 * @package Brainformatik\CalDAV\Enum
 */
class EventTransparency extends AbstractEnum {
    const OPAQUE =      'OPAQUE';      // blocks or opaque on busy time searches
    const TRANSPARENT = 'TRANSPARENT'; // transparent on busy time searches
}