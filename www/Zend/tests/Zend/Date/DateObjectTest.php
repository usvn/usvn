<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Date
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DateObjectTest.php 4786 2007-05-12 15:31:30Z thomas $
 */


/**
 * Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */
class Zend_Date_DateObjectTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        date_default_timezone_set('Europe/Paris');
    }

    /**
     * Test for date object creation null value
     */
    public function testCreationNull()
    {
        $date = new Zend_Date(0);
        $this->assertTrue($date instanceof Zend_Date, "expected Zend_Date object");
    }

    /**
     * Test for date object creation negative timestamp
     */
    public function testCreationNegative()
    {
        $date = new Zend_Date(1000);
        $this->assertTrue($date instanceof Zend_Date, "expected Zend_Date object");
    }

    /**
     * Test for date object creation text given
     */
    public function testCreationFailed()
    {
        try {
            $date = new Zend_Date("notimestamp");
            $this->fail("exception expected");
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for setUnixTimestamp
     */
    public function testsetUnixTimestamp()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $diff = abs(time() - $date->getUnixTimestamp());
        $this->assertTrue(($diff < 2), "Zend_Date->setUnixTimestamp() returned a significantly "
            . "different timestamp than expected: $diff seconds");
        $date->setUnixTimestamp(0);
        $this->assertSame((string)$date->setUnixTimestamp("12345678901234567890"), '0');
        $this->assertSame((string)$date->setUnixTimestamp("12345678901234567890"), "12345678901234567890");

        $date->setUnixTimestamp();
        $diff = abs(time() - $date->getUnixTimestamp());
        $this->assertTrue($diff < 2, "setUnixTimestamp has a significantly different time than returned by time()): $diff seconds");
    }

    /**
     * Test for setUnixTimestampFailed
     */
    public function testsetUnixTimestampFailed()
    {
        try {
            $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
            $date->setUnixTimestamp("notimestamp");
            $this->fail("exception expected");
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for getUnixTimestamp
     */
    public function testgetUnixTimestamp()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $result = $date->getUnixTimestamp();
        $diff = abs($result - time());
        $this->assertTrue($diff < 2, "Instance of Zend_Date_DateObject has a significantly different time than returned by setTime(): $diff seconds");
    }

    /**
     * Test for mktime
     */
    public function testMkTimeforDateValuesInPHPRange()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame($date->mktime(0, 0, 0, 12, 30, 2037, false),   mktime(0, 0, 0, 12, 30, 2037));
        $this->assertSame($date->mktime(0, 0, 0, 12, 30, 2037, true),  gmmktime(0, 0, 0, 12, 30, 2037));

        $this->assertSame($date->mktime(0, 0, 0,  1,  1, 2000, false),   mktime(0, 0, 0,  1,  1, 2000));
        $this->assertSame($date->mktime(0, 0, 0,  1,  1, 2000, true),  gmmktime(0, 0, 0,  1,  1, 2000));

        $this->assertSame($date->mktime(0, 0, 0,  1,  1, 1970, false),   mktime(0, 0, 0,  1,  1, 1970));
        $this->assertSame($date->mktime(0, 0, 0,  1,  1, 1970, true),  gmmktime(0, 0, 0,  1,  1, 1970));

        $this->assertSame($date->mktime(0, 0, 0, 12, 30, 1902, false),   mktime(0, 0, 0, 12, 30, 1902));
        $this->assertSame($date->mktime(0, 0, 0, 12, 30, 1902, true),  gmmktime(0, 0, 0, 12, 30, 1902));
    }

    /**
     * Test for mktime
     */
    public function testMkTimeforDateValuesGreaterPHPRange()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame($date->mktime(0, 0, 0,10, 1, 2040, false), 2232658800);
        $this->assertSame($date->mktime(0, 0, 0,10, 1, 2040, true),  2232662400);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 2200, false), 7258114800);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 2200, true),  7258118400);
        $this->assertSame($date->mktime(0, 0, 0,10,10, 2500, false), 16749586800);
        $this->assertSame($date->mktime(0, 0, 0,10,10, 2500, true),  16749590400);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 3000, false), 32503676400);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 3000, true),  32503680000);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 5000, false), 95617580400);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 5000, true),  95617584000);

        // test for different set external timezone
        // the internal timezone should always be used for calculation
        $date->setTimezone('Europe/Paris');
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 2020, true), 1577836800);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 2020, false), 1577833200);
        date_default_timezone_set('Indian/Maldives');
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 2020, true), 1577836800);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 2020, false), 1577836800);
    }

    /**
     * Test for mktime
     */
    public function testMkTimeforDateValuesSmallerPHPRange()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1900, false), -2208992400);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1900, true),  -2208988800);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1700, false), -8520339600);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1700, true),  -8520336000);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1500, false), -14830995600);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1500, true),  -14830992000);
        $this->assertSame($date->mktime(0, 0, 0,10,10, 1582, false), -12219321600);
        $this->assertSame($date->mktime(0, 0, 0,10,10, 1582, true),  -12219321600);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1000, false), -30609795600);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1, 1000, true),  -30609792000);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1,    0, false), -62167395600);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1,    0, true),  -62167392000);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1,-2000, false), -125282595600);
        $this->assertSame($date->mktime(0, 0, 0, 1, 1,-2000, true),  -125282592000);

        $this->assertSame($date->mktime(0, 0, 0, 13, 1, 1899, false), -2208992400);
        $this->assertSame($date->mktime(0, 0, 0, 13, 1, 1899, true),  -2208988800);
        $this->assertSame($date->mktime(0, 0, 0,-11, 1, 1901, false), -2208992400);
        $this->assertSame($date->mktime(0, 0, 0,-11, 1, 1901, true),  -2208988800);
    }

    public function testIsLeapYear()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame($date->checkLeapYear(2000), true);
        $this->assertSame($date->checkLeapYear(2002), false);
        $this->assertSame($date->checkLeapYear(2004), true);
        $this->assertSame($date->checkLeapYear(1899), false);
        $this->assertSame($date->checkLeapYear(1500), true);
        $this->assertSame($date->checkLeapYear(1455), false);
    }

    public function testWeekNumber()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame($date->weekNumber(2000, 1, 1), (int) date('W',mktime(0, 0, 0, 1, 1, 2000)));
        $this->assertSame($date->weekNumber(2020, 10, 1), (int) date('W',mktime(0, 0, 0, 10, 1, 2020)));
        $this->assertSame($date->weekNumber(2005, 5, 15), (int) date('W',mktime(0, 0, 0, 5, 15, 2005)));
        $this->assertSame($date->weekNumber(1994, 11, 22), (int) date('W',mktime(0, 0, 0, 11, 22, 1994)));
        $this->assertSame($date->weekNumber(2000, 12, 31), (int) date('W',mktime(0, 0, 0, 12, 31, 2000)));
        $this->assertSame($date->weekNumber(2050, 12, 31), 52);
        $this->assertSame($date->weekNumber(2050, 6, 6), 23);
        $this->assertSame($date->weekNumber(2056, 1, 1), 52);
        $this->assertSame($date->weekNumber(2049, 12, 31), 52);
        $this->assertSame($date->weekNumber(2048, 12, 31), 53);
        $this->assertSame($date->weekNumber(2047, 12, 31), 1);
    }

    public function testDayOfWeek()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 1), (int) date('w',mktime(0, 0, 0, 1, 1, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 2), (int) date('w',mktime(0, 0, 0, 1, 2, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 3), (int) date('w',mktime(0, 0, 0, 1, 3, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 4), (int) date('w',mktime(0, 0, 0, 1, 4, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 5), (int) date('w',mktime(0, 0, 0, 1, 5, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 6), (int) date('w',mktime(0, 0, 0, 1, 6, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 7), (int) date('w',mktime(0, 0, 0, 1, 7, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2000, 1, 8), (int) date('w',mktime(0, 0, 0, 1, 8, 2000)));
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 1), 6);
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 2), 0);
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 3), 1);
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 4), 2);
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 5), 3);
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 6), 4);
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 7), 5);
        $this->assertSame($date->dayOfWeekHelper(2050, 1, 8), 6);
        $this->assertSame($date->dayOfWeekHelper(1500, 1, 1), 4);
    }

    public function testCalcSunInternal()
    {
        $date = new Zend_Date_DateObjectTestHelper(10000000);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, true),  9961681);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, false), 10010367);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, true),  9967006);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, false), 10005042);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, true),  9947773);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, false), 9996438);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, true),  9953077);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, false), 9991134);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, true),  9923795);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, false), 9972422);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, true),  9929062);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, false), 9967155);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, true),  9985660);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, false), 10034383);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, true),  9991022);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, false), 10029021);

        $date = new Zend_Date_DateObjectTestHelper(-14830988400);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, true),  -14830958811);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, false), -14830924484);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, true),  -14830968296);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, false), -14830915016);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, true),  -14830972733);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, false), -14830938411);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, true),  -14830982224);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, false), -14830928938);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, true),  -14830910336);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, false), -14830962424);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, true),  -14830919837);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, false), -14830952941);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, true),  -14830934808);
        $this->assertSame($date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, false), -14830986871);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, true),  -14830944283);
        $this->assertSame($date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, false), -14830977414);
    }

    public function testGetDate()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);
        $this->assertTrue(is_array($date->getDateParts()));
        $this->assertTrue(is_array($date->getDateParts(1000000)));

        $test = array(             'seconds' => 40,        'minutes' => 46,
            'hours'   => 14,       'mday'    => 12,        'wday'    => 1,
            'mon'     => 1,        'year'    => 1970,      'yday'    => 11,
            'weekday' => 'Monday', 'month'   => 'January', 0         => 1000000);
        $result = $date->getDateParts(1000000);

        $this->assertSame((int) $result['seconds'], (int) $test['seconds']);
        $this->assertSame((int) $result['minutes'], (int) $test['minutes']);
        $this->assertSame((int) $result['hours'],   (int) $test['hours']);
        $this->assertSame((int) $result['mday'],    (int) $test['mday']);
        $this->assertSame((int) $result['wday'],    (int) $test['wday']);
        $this->assertSame((int) $result['mon'],     (int) $test['mon']);
        $this->assertSame((int) $result['year'],    (int) $test['year']);
        $this->assertSame((int) $result['yday'],    (int) $test['yday']);
        $this->assertSame($result['weekday'],             $test['weekday']);
        $this->assertSame($result['month'],               $test['month']);
        $this->assertSame($result[0],                     $test[0]);

        $test = array(               'seconds' => 20,        'minutes' => 33,
            'hours'   => 11,         'mday'    => 6,        'wday'    => 3,
            'mon'     => 3,          'year'    => 1748,      'yday'    => 65,
            'weekday' => 'Wednesday', 'month'   => 'February', 0        => -7000000000);
        $result = $date->getDateParts(-7000000000);

        $this->assertSame((int) $result['seconds'], (int) $test['seconds']);
        $this->assertSame((int) $result['minutes'], (int) $test['minutes']);
        $this->assertSame((int) $result['hours'],   (int) $test['hours']);
        $this->assertSame((int) $result['mday'],    (int) $test['mday']);
        $this->assertSame((int) $result['wday'],    (int) $test['wday']);
        $this->assertSame((int) $result['mon'],     (int) $test['mon']);
        $this->assertSame((int) $result['year'],    (int) $test['year']);
        $this->assertSame((int) $result['yday'],    (int) $test['yday']);
        $this->assertSame($result['weekday'],             $test['weekday']);
        $this->assertSame($result['month'],               $test['month']);
        $this->assertSame($result[0],                     $test[0]);

        $test = array(               'seconds' => 0,        'minutes' => 40,
            'hours'   => 2,          'mday'    => 26,       'wday'    => 2,
            'mon'     => 8,          'year'    => 2188,     'yday'    => 238,
            'weekday' => 'Tuesday', 'month'   => 'July', 0      => 6900000000);
        $result = $date->getDateParts(6900000000);

        $this->assertSame((int) $result['seconds'], (int) $test['seconds']);
        $this->assertSame((int) $result['minutes'], (int) $test['minutes']);
        $this->assertSame((int) $result['hours'],   (int) $test['hours']);
        $this->assertSame((int) $result['mday'],    (int) $test['mday']);
        $this->assertSame((int) $result['wday'],    (int) $test['wday']);
        $this->assertSame((int) $result['mon'],     (int) $test['mon']);
        $this->assertSame((int) $result['year'],    (int) $test['year']);
        $this->assertSame((int) $result['yday'],    (int) $test['yday']);
        $this->assertSame($result['weekday'],             $test['weekday']);
        $this->assertSame($result['month'],               $test['month']);
        $this->assertSame($result[0],                     $test[0]);

        $test = array(               'seconds' => 0,        'minutes' => 40,
            'hours'   => 2,          'mday'    => 26,       'wday'    => 3,
            'mon'     => 8,          'year'    => 2188,     'yday'    => 238,
            'weekday' => 'Wednesday', 'month'   => 'July', 0      => 6900000000);
        $result = $date->getDateParts(6900000000, true);

        $this->assertSame((int) $result['seconds'], (int) $test['seconds']);
        $this->assertSame((int) $result['minutes'], (int) $test['minutes']);
        $this->assertSame((int) $result['hours'],   (int) $test['hours']);
        $this->assertSame((int) $result['mday'],    (int) $test['mday']);
        $this->assertSame((int) $result['mon'],     (int) $test['mon']);
        $this->assertSame((int) $result['year'],    (int) $test['year']);
        $this->assertSame((int) $result['yday'],    (int) $test['yday']);
    }
    
    public function testDate()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);
        $this->assertTrue($date->date('U') > 0);
        $this->assertSame($date->date('U',0),'0');
        $this->assertSame($date->date('U',0,false),'0');
        $this->assertSame($date->date('U',0,true),'0');
        $this->assertSame($date->date('U',6900000000),'6900003600');
        $this->assertSame($date->date('U',-7000000000),'-6999996400');
        $this->assertSame($date->date('d',-7000000000),'06');
        $this->assertSame($date->date('D',-7000000000),'Wed');
        $this->assertSame($date->date('j',-7000000000),'6');
        $this->assertSame($date->date('l',-7000000000),'Wednesday');
        $this->assertSame($date->date('N',-7000000000),'3');
        $this->assertSame($date->date('S',-7000000000),'th');
        $this->assertSame($date->date('w',-7000000000),'3');
        $this->assertSame($date->date('z',-7000000000),'65');
        $this->assertSame($date->date('W',-7000000000),'10');
        $this->assertSame($date->date('F',-7000000000),'March');
        $this->assertSame($date->date('m',-7000000000),'03');
        $this->assertSame($date->date('M',-7000000000),'Mar');
        $this->assertSame($date->date('n',-7000000000),'3');
        $this->assertSame($date->date('t',-7000000000),'31');
        $this->assertSame($date->date('T',-7000000000),'CET');
        $this->assertSame($date->date('L',-7000000000),'1');
        $this->assertSame($date->date('o',-7000000000),'1748');
        $this->assertSame($date->date('Y',-7000000000),'1748');
        $this->assertSame($date->date('y',-7000000000),'48');
        $this->assertSame($date->date('a',-7000000000),'pm');
        $this->assertSame($date->date('A',-7000000000),'PM');
        $this->assertSame($date->date('B',-7000000000),'523');
        $this->assertSame($date->date('g',-7000000000),'12');
        $this->assertSame($date->date('G',-7000000000),'12');
        $this->assertSame($date->date('h',-7000000000),'12');
        $this->assertSame($date->date('H',-7000000000),'12');
        $this->assertSame($date->date('i',-7000000000),'33');
        $this->assertSame($date->date('s',-7000000000),'20');
        $this->assertSame($date->date('e',-7000000000),'Europe/Paris');
        $this->assertSame($date->date('I',-7000000000),'0');
        $this->assertSame($date->date('O',-7000000000),'+0100');
        $this->assertSame($date->date('P',-7000000000),'+01:00');
        $this->assertSame($date->date('T',-7000000000),'CET');
        $this->assertSame($date->date('Z',-7000000000),'3600');
        $this->assertSame($date->date('c',-7000000000),'1748-03-06T12:33:20+0100');
        $this->assertSame($date->date('r',-7000000000),'Wed, 06 Mar 1748 12:33:20 +0100');
        $this->assertSame($date->date('U',-7000000000),'-6999996400');
        $this->assertSame($date->date('\\H',-7000000000),'H');
        $this->assertSame($date->date('.',-7000000000),'.');
        $this->assertSame($date->date('H:m:s',-7000000000),'12:03:20');
        $this->assertSame($date->date('d-M-Y',-7000000000),'06-Mar-1748');
        $this->assertSame($date->date('U',6900000000,true),'6900000000');
        $this->assertSame($date->date('B',6900000000,true),'152');
        $this->assertSame($date->date('g',6899993000,true),'12');
        $this->assertSame($date->date('g',6899997000,true),'1');
        $this->assertSame($date->date('g',6900039200,true),'1');
        $this->assertSame($date->date('h',6899993000,true),'12');
        $this->assertSame($date->date('h',6899997000,true),'01');
        $this->assertSame($date->date('h',6900040200,true),'01');
        $this->assertSame($date->date('e',-7000000000,true),'UTC');
        $this->assertSame($date->date('I',-7000000000,true),'0');
        $this->assertSame($date->date('T',-7000000000, true),'GMT');
        $this->assertSame($date->date('N',6899740800, true),'6');
        $this->assertSame($date->date('S',6900518000, true),'st');
        $this->assertSame($date->date('S',6900604800, true),'nd');
        $this->assertSame($date->date('S',6900691200, true),'rd');
        $this->assertSame($date->date('N',6900432000, true),'7');
        $date->setTimezone('Europe/Vienna');
        date_default_timezone_set('Indian/Maldives');
        $reference = $date->date('U');
        $this->assertTrue(abs($reference - time()) < 2);
        $this->assertSame($date->date('U',69000000),'69000000');
        
        // ISO Year (o) depends on the week number so 1.1. can be last year is week is 52/53
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1740)),'1739');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1741)),'1740');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1742)),'1742');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1743)),'1743');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1744)),'1744');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1745)),'1744');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1746)),'1745');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1747)),'1746');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1748)),'1748');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 1749)),'1749');
        $this->assertSame($date->date('o',$date->mktime(0, 0, 0, 1, 1, 2050)),'2049');
    }

    function testMktimeDay0And32()
    {
        // the following functionality is used by isTomorrow() and isYesterday() in Zend_Date.
        list($month, $day, $year) = array(12,32,2005);
        $date = new Zend_Date_DateObjectTestHelper(0);
        $this->assertSame($date->date('Ymd', $date->mktime(0, 0, 0, $month, $day, $year)), '20060101');
        list($month, $day, $year) = array(2,29,2005);
        $this->assertSame($date->date('Ymd', $date->mktime(0, 0, 0, $month, $day, $year)), '20050301');
        list($month, $day, $year) = array(1,0,2006);
        $this->assertSame($date->date('Ymd', $date->mktime(0, 0, 0, $month, $day, $year)), '20051231');
        list($month, $day, $year) = array(2,0,2005);
        $this->assertSame($date->date('Ymd', $date->mktime(0, 0, 0, $month, $day, $year)), '20050131');
    }

    /**
     * Test for setTimezone()
     */
    public function testSetTimezone()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);

        date_default_timezone_set('Europe/Vienna');
        $this->assertTrue($date->setTimezone('Indian/Maldives'));
        $this->assertSame($date->getTimezone(), 'Indian/Maldives');
        try {
            $this->assertFalse($date->setTimezone('Unknown'));
            $this->fail("exception expected");
        } catch (Zend_Date_Exception $e) {
            $this->assertRegexp('/not a known timezone/i', $e->getMessage());
            $this->assertSame($e->getOperand(), 'Unknown');
        }
        $this->assertSame($date->getTimezone(), 'Indian/Maldives');
        $this->assertTrue($date->setTimezone());
        $this->assertSame($date->getTimezone(), 'Europe/Vienna');
        
    }

    /**
     * Test for gmtOffset
     */
    public function testgetGmtOffset()
    {
        $date = new Zend_Date_DateObjectTestHelper(0);
        
        date_default_timezone_set('Europe/Vienna');
        $date->setTimezone();
        
        $this->assertSame($date->getGmtOffset(), -3600);
        $date->setTimezone('GMT');
        $this->assertSame($date->getGmtOffset(), 0);
    }

    /**
     * Test for _getTime
     */
    public function test_getTime()
    {
        $date = new Zend_Date_DateObjectTestHelper(Zend_Date::now());
        $time = $date->_getTime();
        $diff = abs(time() - $time);
        $this->assertTrue(($diff < 2), "Zend_Date_DateObject->_getTime() returned a significantly "
            . "different timestamp than expected: $diff seconds");
    }
}

class Zend_Date_DateObjectTestHelper extends Zend_Date
{
    public function __construct($date = null, $part = null, $locale = null)
    {
        $this->setTimezone('Europe/Paris');
        parent::__construct($date, $part, $locale);
    }

    public function mktime($hour, $minute, $second, $month, $day, $year, $dst= -1, $gmt = false)
    {
        return parent::mktime($hour, $minute, $second, $month, $day, $year, $dst, $gmt);
    }

    public function getUnixTimestamp()
    {
        return parent::getUnixTimestamp();
    }
    
    public function setUnixTimestamp($timestamp = null)
    {
        return parent::setUnixTimestamp($timestamp);
    }

    public function weekNumber($year, $month, $day)
    {
        return parent::weekNumber($year, $month, $day);
    }

    public function dayOfWeekHelper($y, $m, $d)
    {
        return Zend_Date_DateObject::dayOfWeek($y, $m, $d);
    }

    public function calcSun($location, $horizon, $rise = false)
    {
        return parent::calcSun($location, $horizon, $rise);
    }

    public function date($format, $timestamp = null, $gmt = false)
    {
        return parent::date($format, $timestamp, $gmt);
    }

    public function getDateParts($timestamp = null, $fast = null)
    {
        return parent::getDateParts($timestamp, $fast);
    }

    public function _getTime($sync = null)
    {
        return parent::_getTime($sync);
    }
}