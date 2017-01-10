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
use Brainformatik\CalDAV\Entity\Calendar;
use Brainformatik\CalDAV\Entity\Event;
use Brainformatik\CalDAV\Entity\Todo;
use \Brainformatik\CalDAV\Helper\BaseTestCase;

class CalendarTest extends BaseTestCase {

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
    protected static $dummyCalendarUrl = '/calendars/123456780/1/';

    /**
     * @var string
     */
    protected static $dummyCalendarName = 'MyFavouriteCalendar';

    public function testSetGetUrl() {
        $calendar = new Calendar($this->getClient(), self::$dummyCalendarUrl);

        $this->assertException(function () use ($calendar) {
            $calendar->setUrl(123);
        }, InvalidArgumentException::class);

        $this->assertEquals(self::$dummyCalendarUrl, $calendar->getUrl());
    }

    public function testSetGetDisplayname() {
        $calendar = new Calendar($this->getClient(), self::$dummyCalendarUrl);

        $calendar->setDisplayName(self::$dummyCalendarName);

        $this->assertException(function () use ($calendar) {
            $calendar->setDisplayName(123);
        }, InvalidArgumentException::class);

        $this->assertEquals(self::$dummyCalendarName, $calendar->getDisplayName());
    }

    public function testAddEvent() {
        $calendar = new Calendar($this->getClient(), self::$dummyCalendarUrl);
        $this->assertInstanceOf(Event::class, $calendar->addEvent());
    }

    public function testAddTodo() {
        $calendar = new Calendar($this->getClient(), self::$dummyCalendarUrl);
        $this->assertInstanceOf(Todo::class, $calendar->addTodo());
    }

    /**
     * @return Client
     */
    protected function getClient() {
        return new Client(self::$dummyData);
    }
}