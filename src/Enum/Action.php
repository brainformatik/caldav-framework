<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

/**
 * Class Action
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.6.1
 *
 * @package Brainformatik\CalDAV\Enum
 */
class Action extends AbstractEnum {
    const AUDIO = 'AUDIO';
    const DISPLAY = 'DISPLAY';
    const EMAIL = 'EMAIL';
}