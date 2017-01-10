<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Enum;

class HttpMethod extends AbstractEnum {
    const OPTIONS   = 'OPTIONS';
    const GET       = 'GET';
    const HEAD      = 'HEAD';
    const POST      = 'POST';
    const PUT       = 'PUT';
    const DELETE    = 'DELETE';
    const TRACE     = 'TRACE';
    const COPY      = 'COPY';
    const MOVE      = 'MOVE';
    const PROPFIND  = 'PROPFIND';
    const PROPPATCH = 'PROPPATCH';
    const LOCK      = 'LOCK';
    const UNLOCK    = 'UNLOCK';
    const REPORT    = 'REPORT';
    const ACL       = 'ACL';
}