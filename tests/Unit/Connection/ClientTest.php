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

use Brainformatik\CalDAV\Connection\Client;
use \Brainformatik\CalDAV\Helper\BaseTestCase;

class ClientTest extends BaseTestCase {

    /**
     * @var array
     */
    protected static $dummyData = [
        'baseUri' => 'http://127.0.0.1/caldav/',
        'userName' => 'defaultUser',
        'password' => 'defaultPassword'
    ];

    public function testGetBaseUrl() {
        $client = new Client(self::$dummyData);
        $this->assertEquals(self::$dummyData['baseUri'], $client->getBaseUrl());
    }

    public function testIsValidConnection() {
        $clientMockInvalid = $this->getMockBuilder(Client::class)
            ->setMethods([
                'options'
            ])
            ->setConstructorArgs([self::$dummyData])
            ->getMock();
        $clientMockInvalid->method('options')->will($this->returnValue([]));

        $this->assertFalse($clientMockInvalid->isValidConnection());

        $clientMockValid = $this->getMockBuilder(Client::class)
            ->setMethods([
                'options'
            ])
            ->setConstructorArgs([self::$dummyData])
            ->getMock();
        $clientMockValid->method('options')->will($this->returnValue(['calendar-access']));

        $this->assertTrue($clientMockValid->isValidConnection());
    }
}