<?php

/**
 * Copyright (C) 2015 Brainformatik GmbH (info@brainformatik.com)
 *
 * This file is part of CRM+ Software.
 * All Rights Reserved.
 *
 * This part of CRM+ can not be copied and/or distributed without the
 * express permission of Brainformatik GmbH.
 */

use \Brainformatik\CalDAV\Helper\BaseTestCase;

class PrincipalTest extends BaseTestCase {
    /**
     * @var array
     */
    protected static $dummyData = [
        'baseUri' => 'http://127.0.0.1/caldav/',
        'userName' => 'defaultUser',
        'password' => 'defaultPassword'
    ];

    /**
     * @var string
     */
    protected static $dummyPrincipalUrl = '/principals/123456789/';

    public function testSetGetUrl() {
        $principal = new \Brainformatik\CalDAV\Connection\Principal($this->getClient(), self::$dummyPrincipalUrl);

        $this->assertException(function () use ($principal) {
            $principal->setUrl(123);
        }, InvalidArgumentException::class);

        $this->assertEquals(self::$dummyPrincipalUrl, $principal->getUrl());
    }

    /**
     * @return \Brainformatik\CalDAV\Connection\Client
     */
    protected function getClient() {
        return new \Brainformatik\CalDAV\Connection\Client(self::$dummyData);
    }
}