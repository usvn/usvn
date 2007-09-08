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
 * @version    $Id: DateTest.php 5790 2007-07-19 20:04:11Z thomas $
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite
/**
 * These const values control some testing behavior.
 * They may be defined here or in TestConfiguration.php.
 */
if (!defined('TESTS_ZEND_LOCALE_BCMATH_ENABLED')) {
    // Set to false to disable usage of bcmath extension by Zend_Date
    define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', true);
}
if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE')) {
    // Set to true to run full Zend_Date unit tests.
    // Set to false to run a good subset of Zend_Date unit tests.
    define('TESTS_ZEND_I18N_EXTENDED_COVERAGE', true);
}


/**
 * Zend_Date
 */
require_once 'Zend/Loader.php';
require_once 'Zend/Date.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Date/Cities.php';
// require_once 'Zend/TimeSync.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";


/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */
class Zend_DateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        date_default_timezone_set('Indian/Maldives');
    }

    /**
     * Test for date object creation
     */
    public function testCreation()
    {
        $date = new Zend_Date(0);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for date object creation using default format for a locale
     */
    public function testCreationDefaultFormat()
    {
        $date  = new Zend_Date('2006-01-01');
        $this->assertTrue($date instanceof Zend_Date);
        $this->assertSame($date->get(Zend_Date::ISO_8601), '2006-01-01T00:00:00+05:00');

        $date  = new Zend_Date('2006-01-01', 'en_US');
        $this->assertTrue($date instanceof Zend_Date);
        $this->assertSame($date->get(Zend_Date::ISO_8601), '2006-01-01T00:00:00+05:00');
    }

    /**
     * Test for date object creation using default format for a locale
     */
    public function testCreationDefaultFormatConsistency()
    {
        date_default_timezone_set('America/New_York');
        $locale = 'en_US';
        //2006-01-01T00:00:00+05:00
        $date1  = new Zend_Date('2006-01-01 01:00:00', Zend_Date::ISO_8601, $locale);
        $date1string = $date1->get(Zend_Date::ISO_8601);

        // en_US defines AM/PM, hour 0 does not exist
        // ISO defines dates without AM, 0 exists instead of 12 PM
        // therefor hour is set to 1 to verify
        $date2  = new Zend_Date('2006-01-01', Zend_Date::DATES, $locale);
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
        $date2  = new Zend_Date('01-01-2006', Zend_Date::DATES, $locale);
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
        $date2  = new Zend_Date('2006-01-01', null, $locale);
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
        $date2  = new Zend_Date('2006-01-01');
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
    }

    /**
     * Test for creation with timestamp
     */
    public function testCreationTimestamp()
    {
        $date = new Zend_Date('12345678');
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for creation but only part of date
     */
    public function testCreationDatePart()
    {
        $date = new Zend_Date('13',Zend_Date::HOUR);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for creation but only a defined locale
     */
    public function testCreationLocale()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date('13',null,$locale);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for creation but only part of date with locale
     */
    public function testCreationLocalePart()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date('13',Zend_Date::HOUR,$locale);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for date object creation using default format for a locale
     */
    public function testCreationDefaultLoose()
    {
        $locale = 'de_AT';
        $date  = new Zend_Date();

        $date = $date->getTimestamp();
        $this->assertTrue(abs($date - time()) < 2);

        date_default_timezone_set('GMT');
        $date  = new Zend_Date(Zend_Date::YEAR);

        $date = $date->getTimestamp();
        $reference = gmmktime(0,0,0,1,1,date('Y'));
        $this->assertTrue($reference == $date);

        $date  = new Zend_Date('ar_EG');
        $this->assertSame($date->getLocale(), 'ar_EG');
        $date = $date->getTimestamp();
        $this->assertTrue(abs($date - time()) < 2);
    }

    /**
     * Test for getTimestamp
     */
    public function testGetTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(10000000);
        $this->assertSame($date->getTimestamp(), 10000000);
    }

    /**
     * Test for getUnixTimestamp
     */
    public function testgetUnixTimestamp2()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(-100000000);
        $this->assertSame($date->getTimestamp(), -100000000);
    }

    /**
     * Test for setTimestamp
     */
    public function testSetTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
        $result = $date->setTimestamp(10000000);
        $this->assertSame((string)$result->getTimestamp(), '10000000');
    }

    /**
     * Test for setTimestamp
     */
    public function testSetTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
            $date = new Zend_Date(0,null,$locale);
            $result = $date->setTimestamp('notimestamp');
            $this->Fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for addTimestamp
     */
    public function testAddTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $result = $date->addTimestamp(10000000);
        $this->assertSame((string)$result->getTimestamp(), '10000000');
    }

    /**
     * Test for addTimestamp
     */
    public function testAddTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
            $date = new Zend_Date(0,null,$locale);
            $result = $date->addTimestamp('notimestamp');
            $this->Fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for subTimestamp
     */
    public function testSubTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $result = $date->subTimestamp(10000000);
        $this->assertSame((string)$result->getTimestamp(), '-10000000');
    }

    /**
     * Test for subTimestamp
     */
    public function testSubTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
            $date = new Zend_Date(0,null,$locale);
            $result = $date->subTimestamp('notimestamp');
            $this->Fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for compareTimestamp
     */
    public function testCompareTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date1 = new Zend_Date(0,null,$locale);
        $date2 = new Zend_Date(0,null,$locale);
        $this->assertSame($date1->compareTimestamp($date2), 0);

        $date2 = new Zend_Date(100,null,$locale);
        $this->assertSame($date1->compareTimestamp($date2), -1);

        $date2 = new Zend_Date(-100,null,$locale);
        $this->assertSame($date1->compareTimestamp($date2), 1);
    }

    /**
     * Test for __toString
     */
    public function test_ToString()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $this->assertSame($date->__toString(),'01.01.1970 05:00:00');
    }

    /**
     * Test for toString
     */
    public function testToString()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $this->assertSame($date->toString(),'14.02.2009 04:31:30');
        $this->assertSame($date->toString('en_US'),'Feb 14, 2009 4:31:30 AM');
        $this->assertSame($date->toString(null, 'en_US'),'Feb 14, 2009 4:31:30 AM');
        $this->assertSame($date->toString('yyy', null),'2009');
        $this->assertSame($date->toString(null, null),'14.02.2009 04:31:30');
        $date->setTimeZone('UTC');
        $this->assertSame($date->toString(null, 'en_US'),'Feb 13, 2009 11:31:30 PM');
        $date->setTimeZone('Indian/Maldives');
        $this->assertSame($date->toString("xx'yy''yy'xx"),"xxyy'yyxx");
        $this->assertSame($date->toString("GGGGG"),'n.');
        $this->assertSame($date->toString("GGGG"),'n. Chr.');
        $this->assertSame($date->toString("GGG"),'n. Chr.');
        $this->assertSame($date->toString("GG"),'n. Chr.');
        $this->assertSame($date->toString("G"),'n. Chr.');
        $this->assertSame($date->toString("yyyyy"),'02009');
        $this->assertSame($date->toString("yyyy"),'2009');
        $this->assertSame($date->toString("yyy"),'2009');
        $this->assertSame($date->toString("yy"),'09');
        $this->assertSame($date->toString("y"),'2009');
        $this->assertSame($date->toString("YYYYY"),'02009');
        $this->assertSame($date->toString("YYYY"),'2009');
        $this->assertSame($date->toString("YYY"),'2009');
        $this->assertSame($date->toString("YY"),'09');
        $this->assertSame($date->toString("Y"),'2009');
        $this->assertSame($date->toString("MMMMM"),'F');
        $this->assertSame($date->toString("MMMM"),'Februar');
        $this->assertSame($date->toString("MMM"),'Feb');
        $this->assertSame($date->toString("MM"),'02');
        $this->assertSame($date->toString("M"),'2');
        $this->assertSame($date->toString("ww"),'07');
        $this->assertSame($date->toString("w"),'07');
        $this->assertSame($date->toString("dd"),'14');
        $this->assertSame($date->toString("d"),'14');
        $this->assertSame($date->toString("DDD"),'044');
        $this->assertSame($date->toString("DD"),'44');
        $this->assertSame($date->toString("D"),'44');
        $this->assertSame($date->toString("EEEEE"),'S');
        $this->assertSame($date->toString("EEEE"),'Samstag');
        $this->assertSame($date->toString("EEE"),'Sam');
        $this->assertSame($date->toString("EE"),'Sa');
        $this->assertSame($date->toString("E"),'S');
        $this->assertSame($date->toString("ee"),'06');
        $this->assertSame($date->toString("e"),'6');
        $this->assertSame($date->toString("a"),'vorm.');
        $this->assertSame($date->toString("hh"),'04');
        $this->assertSame($date->toString("h"),'4');
        $this->assertSame($date->toString("HH"),'04');
        $this->assertSame($date->toString("H"),'4');
        $this->assertSame($date->toString("mm"),'31');
        $this->assertSame($date->toString("m"),'31');
        $this->assertSame($date->toString("ss"),'30');
        $this->assertSame($date->toString("s"),'30');
        $this->assertSame($date->toString("S"),'0');
        $this->assertSame($date->toString("zzzz"),'Indian/Maldives');
        $this->assertSame($date->toString("zzz"),'MVT');
        $this->assertSame($date->toString("zz"),'MVT');
        $this->assertSame($date->toString("z"),'MVT');
        $this->assertSame($date->toString("ZZZZ"),'+05:00');
        $this->assertSame($date->toString("ZZZ"),'+0500');
        $this->assertSame($date->toString("ZZ"),'+0500');
        $this->assertSame($date->toString("Z"),'+0500');
        $this->assertSame($date->toString("AAAAA"),'16290');
        $this->assertSame($date->toString("AAAA"),'16290');
        $this->assertSame($date->toString("AAA"),'16290');
        $this->assertSame($date->toString("AA"),'16290');
        $this->assertSame($date->toString("A"),'16290');
    }

    /**
     * Test for toValue
     */
    public function testToValue()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $this->assertSame($date->toValue(),1234567890);
        $this->assertSame($date->toValue(Zend_Date::DAY),14);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DAY),13);
        $date->setTimezone('Indian/Maldives');
        //Friday, 13-Feb-09 23:31:30 UTC
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_SHORT),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_SHORT),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DAY_SHORT),14);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DAY_SHORT),13);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_8601),6);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_8601),5);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DAY_SUFFIX),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DAY_SUFFIX),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_DIGIT),6);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_DIGIT),5);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DAY_OF_YEAR),44);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DAY_OF_YEAR),43);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_NARROW),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_NARROW),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_NAME),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::WEEKDAY_NAME),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::WEEK),7);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::WEEK),7);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MONTH_NAME),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MONTH_NAME),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MONTH),2);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MONTH),2);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MONTH_NAME_SHORT),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MONTH_NAME_SHORT),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MONTH_SHORT),2);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MONTH_SHORT),2);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MONTH_DAYS),28);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MONTH_DAYS),28);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MONTH_NAME_NARROW),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MONTH_NAME_NARROW),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::LEAPYEAR),0);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::LEAPYEAR),0);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::YEAR_8601),2009);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::YEAR_8601),2009);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::YEAR),2009);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::YEAR),2009);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::YEAR_SHORT),9);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::YEAR_SHORT),9);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::YEAR_SHORT_8601),9);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::YEAR_SHORT_8601),9);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MERIDIEM),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MERIDIEM),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::SWATCH),21);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::SWATCH),21);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::HOUR_SHORT_AM),4);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::HOUR_SHORT_AM),11);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::HOUR_SHORT),4);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::HOUR_SHORT),23);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::HOUR_AM),4);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::HOUR_AM),11);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::HOUR),4);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::HOUR),23);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MINUTE),31);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MINUTE),31);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::SECOND),30);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::SECOND),30);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MILLISECOND),0);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MILLISECOND),0);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::MINUTE_SHORT),31);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::MINUTE_SHORT),31);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::SECOND_SHORT),30);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::SECOND_SHORT),30);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIMEZONE_NAME),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIMEZONE_NAME),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DAYLIGHT),0);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DAYLIGHT),0);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::GMT_DIFF),500);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::GMT_DIFF),0);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::GMT_DIFF_SEP),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::GMT_DIFF_SEP),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIMEZONE),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIMEZONE),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIMEZONE_SECS),18000);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIMEZONE_SECS),0);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::ISO_8601),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::ISO_8601),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::RFC_2822),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::RFC_2822),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIMESTAMP),1234567890);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIMESTAMP),1234567890);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::ERA),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::ERA),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::ERA_NAME),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::ERA_NAME),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DATES),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DATES),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DATE_FULL),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DATE_FULL),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DATE_LONG),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DATE_LONG),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DATE_MEDIUM),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DATE_MEDIUM),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::DATE_SHORT),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::DATE_SHORT),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIMES),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIMES),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIME_FULL),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIME_FULL),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIME_LONG),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIME_LONG),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIME_MEDIUM),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIME_MEDIUM),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::TIME_SHORT),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::TIME_SHORT),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::ATOM),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::ATOM),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::COOKIE),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::COOKIE),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::RFC_822),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::RFC_822),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::RFC_850),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::RFC_850),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::RFC_1036),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::RFC_1036),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::RFC_1123),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::RFC_1123),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::RFC_3339),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::RFC_3339),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::RSS),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::RSS),false);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->toValue(Zend_Date::W3C),false);
        $date->setTimezone('UTC');
        $this->assertSame($date->toValue(Zend_Date::W3C),false);
    }

    /**
     * Test for toValue
     */
    public function testGet()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->get(),1234567890);

        $this->assertSame($date->get(Zend_Date::DAY),'14');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DAY),'13');
        $this->assertSame($date->get(Zend_Date::DAY, 'es'),'13');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::WEEKDAY_SHORT),'Sam');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_SHORT),'Fre');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_SHORT, 'es'),'vie');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DAY_SHORT),'14');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DAY_SHORT),'13');
        $this->assertSame($date->get(Zend_Date::DAY_SHORT, 'es'),'13');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::WEEKDAY),'Samstag');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::WEEKDAY),'Freitag');
        $this->assertSame($date->get(Zend_Date::WEEKDAY, 'es'),'viernes');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::WEEKDAY_8601),'6');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_8601),'5');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_8601, 'es'),'5');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DAY_SUFFIX),'th');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DAY_SUFFIX),'th');
        $this->assertSame($date->get(Zend_Date::DAY_SUFFIX, 'es'),'th');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::WEEKDAY_DIGIT),'6');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_DIGIT),'5');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_DIGIT, 'es'),'5');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DAY_OF_YEAR),'44');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DAY_OF_YEAR),'43');
        $this->assertSame($date->get(Zend_Date::DAY_OF_YEAR, 'es'),'43');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::WEEKDAY_NARROW),'S');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_NARROW),'F');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_NARROW, 'es'),'v');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::WEEKDAY_NAME),'Sa');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_NAME),'Fr');
        $this->assertSame($date->get(Zend_Date::WEEKDAY_NAME, 'es'),'vie');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::WEEK),'07');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::WEEK),'07');
        $this->assertSame($date->get(Zend_Date::WEEK, 'es'),'07');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MONTH_NAME),'Februar');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MONTH_NAME),'Februar');
        $this->assertSame($date->get(Zend_Date::MONTH_NAME, 'es'),'febrero');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MONTH),'02');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MONTH),'02');
        $this->assertSame($date->get(Zend_Date::MONTH, 'es'),'02');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MONTH_NAME_SHORT),'Feb');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MONTH_NAME_SHORT),'Feb');
        $this->assertSame($date->get(Zend_Date::MONTH_NAME_SHORT, 'es'),'feb');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MONTH_SHORT),'2');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MONTH_SHORT),'2');
        $this->assertSame($date->get(Zend_Date::MONTH_SHORT, 'es'),'2');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MONTH_DAYS),'28');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MONTH_DAYS),'28');
        $this->assertSame($date->get(Zend_Date::MONTH_DAYS, 'es'),'28');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MONTH_NAME_NARROW),'F');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MONTH_NAME_NARROW),'F');
        $this->assertSame($date->get(Zend_Date::MONTH_NAME_NARROW, 'es'),'f');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::LEAPYEAR),'0');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::LEAPYEAR),'0');
        $this->assertSame($date->get(Zend_Date::LEAPYEAR, 'es'),'0');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::YEAR_8601),'2009');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::YEAR_8601),'2009');
        $this->assertSame($date->get(Zend_Date::YEAR_8601, 'es'),'2009');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::YEAR),'2009');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::YEAR),'2009');
        $this->assertSame($date->get(Zend_Date::YEAR, 'es'),'2009');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::YEAR_SHORT),'09');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::YEAR_SHORT),'09');
        $this->assertSame($date->get(Zend_Date::YEAR_SHORT, 'es'),'09');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::YEAR_SHORT_8601),'09');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::YEAR_SHORT_8601),'09');
        $this->assertSame($date->get(Zend_Date::YEAR_SHORT_8601, 'es'),'09');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MERIDIEM),'vorm.');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MERIDIEM),'nachm.');
        $this->assertSame($date->get(Zend_Date::MERIDIEM, 'es'),'p.m.');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::SWATCH),'021');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::SWATCH),'021');
        $this->assertSame($date->get(Zend_Date::SWATCH, 'es'),'021');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::HOUR_SHORT_AM),'4');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::HOUR_SHORT_AM),'11');
        $this->assertSame($date->get(Zend_Date::HOUR_SHORT_AM, 'es'),'11');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::HOUR_SHORT),'4');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::HOUR_SHORT),'23');
        $this->assertSame($date->get(Zend_Date::HOUR_SHORT, 'es'),'23');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::HOUR_AM),'04');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::HOUR_AM),'11');
        $this->assertSame($date->get(Zend_Date::HOUR_AM, 'es'),'11');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::HOUR),'04');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::HOUR),'23');
        $this->assertSame($date->get(Zend_Date::HOUR, 'es'),'23');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MINUTE),'31');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MINUTE),'31');
        $this->assertSame($date->get(Zend_Date::MINUTE, 'es'),'31');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::SECOND),'30');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::SECOND),'30');
        $this->assertSame($date->get(Zend_Date::SECOND, 'es'),'30');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);
        $this->assertSame($date->get(Zend_Date::MILLISECOND, 'es'),0);
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::MINUTE_SHORT),'31');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::MINUTE_SHORT),'31');
        $this->assertSame($date->get(Zend_Date::MINUTE_SHORT, 'es'),'31');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::SECOND_SHORT),'30');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::SECOND_SHORT),'30');
        $this->assertSame($date->get(Zend_Date::SECOND_SHORT, 'es'),'30');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIMEZONE_NAME),'Indian/Maldives');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIMEZONE_NAME),'UTC');
        $this->assertSame($date->get(Zend_Date::TIMEZONE_NAME, 'es'),'UTC');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DAYLIGHT),'0');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DAYLIGHT),'0');
        $this->assertSame($date->get(Zend_Date::DAYLIGHT, 'es'),'0');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::GMT_DIFF),'+0500');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::GMT_DIFF),'+0000');
        $this->assertSame($date->get(Zend_Date::GMT_DIFF, 'es'),'+0000');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::GMT_DIFF_SEP),'+05:00');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::GMT_DIFF_SEP),'+00:00');
        $this->assertSame($date->get(Zend_Date::GMT_DIFF_SEP, 'es'),'+00:00');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIMEZONE),'MVT');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIMEZONE),'UTC');
        $this->assertSame($date->get(Zend_Date::TIMEZONE, 'es'),'UTC');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIMEZONE_SECS),'18000');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIMEZONE_SECS),'0');
        $this->assertSame($date->get(Zend_Date::TIMEZONE_SECS, 'es'),'0');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::ISO_8601),'2009-02-14T04:31:30+05:00');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::ISO_8601),'2009-02-13T23:31:30+00:00');
        $this->assertSame($date->get(Zend_Date::ISO_8601, 'es'),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::RFC_2822),'Sat, 14 Feb 2009 04:31:30 +0500');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::RFC_2822),'Fri, 13 Feb 2009 23:31:30 +0000');
        $this->assertSame($date->get(Zend_Date::RFC_2822, 'es'),'Fri, 13 Feb 2009 23:31:30 +0000');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIMESTAMP),1234567890);
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIMESTAMP),1234567890);
        $this->assertSame($date->get(Zend_Date::TIMESTAMP, 'es'),1234567890);
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::ERA),'n. Chr.');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::ERA),'n. Chr.');
        $this->assertSame($date->get(Zend_Date::ERA, 'es'),'d.C.');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::ERA_NAME),'n. Chr.');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::ERA_NAME),'n. Chr.');
        $this->assertSame($date->get(Zend_Date::ERA_NAME, 'es'),'CE');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DATES),'14.02.2009');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DATES),'13.02.2009');
        $this->assertSame($date->get(Zend_Date::DATES, 'es'),'13-feb-09');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DATE_FULL),'Samstag, 14. Februar 2009');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DATE_FULL),'Freitag, 13. Februar 2009');
        $this->assertSame($date->get(Zend_Date::DATE_FULL, 'es'),'viernes 13 de febrero de 2009');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DATE_LONG),'14. Februar 2009');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DATE_LONG),'13. Februar 2009');
        $this->assertSame($date->get(Zend_Date::DATE_LONG, 'es'),'13 de febrero de 2009');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DATE_MEDIUM),'14.02.2009');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DATE_MEDIUM),'13.02.2009');
        $this->assertSame($date->get(Zend_Date::DATE_MEDIUM, 'es'),'13-feb-09');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::DATE_SHORT),'14.02.09');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::DATE_SHORT),'13.02.09');
        $this->assertSame($date->get(Zend_Date::DATE_SHORT, 'es'),'13/02/09');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIMES),'04:31:30');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIMES),'23:31:30');
        $this->assertSame($date->get(Zend_Date::TIMES, 'es'),'23:31:30');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIME_FULL),'04:31 Uhr MVT');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIME_FULL),'23:31 Uhr UTC');
        $this->assertSame($date->get(Zend_Date::TIME_FULL, 'es'),'23H3130" UTC');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIME_LONG),'04:31:30 MVT');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIME_LONG),'23:31:30 UTC');
        $this->assertSame($date->get(Zend_Date::TIME_LONG, 'es'),'23:31:30 UTC');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIME_MEDIUM),'04:31:30');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIME_MEDIUM),'23:31:30');
        $this->assertSame($date->get(Zend_Date::TIME_MEDIUM, 'es'),'23:31:30');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::TIME_SHORT),'04:31');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::TIME_SHORT),'23:31');
        $this->assertSame($date->get(Zend_Date::TIME_SHORT, 'es'),'23:31');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::ATOM),'2009-02-14T04:31:30+05:00');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::ATOM),'2009-02-13T23:31:30+00:00');
        $this->assertSame($date->get(Zend_Date::ATOM, 'es'),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::COOKIE),'Saturday, 14-Feb-09 04:31:30 Indian/Maldives');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::COOKIE),'Friday, 13-Feb-09 23:31:30 UTC');
        $this->assertSame($date->get(Zend_Date::COOKIE, 'es'),'Friday, 13-Feb-09 23:31:30 UTC');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::RFC_822),'Sat, 14 Feb 09 04:31:30 +0500');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::RFC_822),'Fri, 13 Feb 09 23:31:30 +0000');
        $this->assertSame($date->get(Zend_Date::RFC_822, 'es'),'Fri, 13 Feb 09 23:31:30 +0000');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::RFC_850),'Saturday, 14-Feb-09 04:31:30 Indian/Maldives');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::RFC_850),'Friday, 13-Feb-09 23:31:30 UTC');
        $this->assertSame($date->get(Zend_Date::RFC_850, 'es'),'Friday, 13-Feb-09 23:31:30 UTC');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::RFC_1036),'Sat, 14 Feb 09 04:31:30 +0500');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::RFC_1036),'Fri, 13 Feb 09 23:31:30 +0000');
        $this->assertSame($date->get(Zend_Date::RFC_1036, 'es'),'Fri, 13 Feb 09 23:31:30 +0000');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::RFC_1123),'Sat, 14 Feb 2009 04:31:30 +0500');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::RFC_1123),'Fri, 13 Feb 2009 23:31:30 +0000');
        $this->assertSame($date->get(Zend_Date::RFC_1123, 'es'),'Fri, 13 Feb 2009 23:31:30 +0000');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::RFC_3339),'2009-02-14T04:31:30+05:00');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::RFC_3339),'2009-02-13T23:31:30+00:00');
        $this->assertSame($date->get(Zend_Date::RFC_3339, 'es'),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::RSS),'Sat, 14 Feb 2009 04:31:30 +0500');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::RSS),'Fri, 13 Feb 2009 23:31:30 +0000');
        $this->assertSame($date->get(Zend_Date::RSS, 'es'),'Fri, 13 Feb 2009 23:31:30 +0000');
        $date->setTimezone('Indian/Maldives');

        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->setTimezone('UTC');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $this->assertSame($date->get(Zend_Date::W3C, 'es'),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        // when get() receives a format string it responses like toString();
        $this->assertSame($date->get('Y'),'2009');
    }

    /**
     * Test for toValue
     */
    public function testGet2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(-62362925370,null,$locale);
        $this->assertSame($date->get(Zend_Date::ERA),'v. Chr.');
        $this->assertSame($date->get(Zend_Date::ERA_NAME),'v. Chr.');
    }

    /**
     * Test for set
     */
    public function testSet()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $d2->setTimezone(date_default_timezone_get());

        $retour = $date->set(1234567890);
        $this->assertSame((string)$retour,'1234567890');
        $this->assertSame((string)$date->set($d2),'1010101010');
        $this->assertSame((string)$date->set(1234567891),'1234567891');

        try {
            $date->set('noday', Zend_Date::DAY);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T04:31:31+05:00');
        $date->set( 10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T04:31:31+05:00');
        $date->set( 40, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-12T04:31:31+05:00');
        $date->set(-10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-18T04:31:31+05:00');
        $date->setTimezone('UTC');
        $date->set( 10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T23:31:31+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set($d2, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T04:31:31+05:00');
        $date->setTimezone('UTC');
        $date->set( 10, Zend_Date::DAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T23:31:31+00:00');
        $date->set($d2, Zend_Date::DAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T23:31:31+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set(-20, Zend_Date::DAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-11T04:31:31+05:00');
        $date->set($d2, Zend_Date::DAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-04T04:31:31+05:00');

        $date->set('10.April.2007', 'dd.MMMM.YYYY');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-04-10T00:00:00+05:00');
    }

    /**
     * Test for set
     */
    public function testSet2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $d2->setTimezone(date_default_timezone_get());

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->set('Son', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T04:31:30+05:00');
        $date->set('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T04:31:30+05:00');
        $date->setTimezone('UTC');
        $date->set('Fre', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set('Thu', Zend_Date::WEEKDAY_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T04:31:30+05:00');
        $date->set($d2, Zend_Date::WEEKDAY_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimezone('UTC');
        $date->set('Wed', Zend_Date::WEEKDAY_SHORT , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('xxx', Zend_Date::DAY_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T04:31:30+05:00');
        $date->set( 10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T04:31:30+05:00');
        $date->set( 40, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-12T04:31:30+05:00');
        $date->set(-10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-18T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T23:31:30+00:00');
        $date->set($d2, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 10, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T04:31:30+05:00');
        $date->set($d2, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-11T23:31:30+00:00');
        $date->set($d2, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-04T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->set('Sonntag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T04:31:30+05:00');
        $date->set('Montag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('Freitag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set('Wednesday', Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T04:31:30+05:00');
        $date->set($d2, Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('Thursday', Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set(0, Zend_Date::WEEKDAY_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set('noday', Zend_Date::WEEKDAY_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->set(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T04:31:30+05:00');
        $date->set(5, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(2, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set(4, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T04:31:30+05:00');
        $date->set($d2, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(3, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set(0, Zend_Date::WEEKDAY_DIGIT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set('noday', Zend_Date::WEEKDAY_DIGIT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->set(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T04:31:30+05:00');
        $date->set(5, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(2, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set(4, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T04:31:30+05:00');
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(3, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DAY_OF_YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T04:31:30+05:00');
        $date->set( 124, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-05-04T04:31:30+05:00');
        $date->set( 524, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-06-08T04:31:30+05:00');
        $date->set(-135, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-18T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 422, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-02-26T23:31:30+00:00');
        $date->set($d2, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-03T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 12, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-12T04:31:30+05:00');
        $date->set($d2, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-03T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-253, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-22T23:31:30+00:00');
        $date->set($d2, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_NARROW);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->set('S', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T04:31:30+05:00');
        $date->set('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('F', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set('W', Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T04:31:30+05:00');
        $date->set($d2, Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('W', Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->set('So', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T04:31:30+05:00');
        $date->set('Mo', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('Fr', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set('Thu', Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T04:31:30+05:00');
        $date->set($d2, Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('Wed', Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEK);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T04:31:30+05:00');
        $date->set( 1, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T04:31:30+05:00');
        $date->set( 55, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-16T04:31:30+05:00');
        $date->set(-57, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2008-11-29T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 50, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2008-12-12T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2008-01-04T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 10, Zend_Date::WEEK, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-03-08T04:31:30+05:00');
        $date->set($d2, Zend_Date::WEEK, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-01-05T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-25, Zend_Date::WEEK, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-07-06T23:31:30+00:00');
        $date->set($d2, Zend_Date::WEEK, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-01-05T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_NAME);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->set('März', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T04:31:30+05:00');
        $date->set('Dezember', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('August', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set('April', Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('July', Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-07-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->set('03', Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T04:31:30+05:00');
        $date->set( 14, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-02-14T04:31:30+05:00');
        $date->set(-6, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-06-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 10, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-10-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MONTH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-09-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::MONTH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::MONTH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-04-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_NAME_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->set('Mär', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T04:31:30+05:00');
        $date->set('Dez', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('Aug', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set('Apr', Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('Jul', Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-07-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->set(  3, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T04:31:30+05:00');
        $date->set( 14, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-02-14T04:31:30+05:00');
        $date->set(-6, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-06-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-10-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-09-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-04-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('xxday', Zend_Date::MONTH_NAME_NARROW);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->set('M', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T04:31:30+05:00');
        $date->set('D', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('A', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set('A', Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set('J', Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->set(1970, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T04:31:30+05:00');
        $date->set(2020, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T04:31:30+05:00');
        $date->set(2040, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(1900, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1900-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set(2500, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2500-02-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'-20-02-14T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->set(1970, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T04:31:30+05:00');
        $date->set(2020, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T04:31:30+05:00');
        $date->set(2040, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(1900, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'1900-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set(2500, Zend_Date::YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2500-02-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'-20-02-14T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->set(70, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T04:31:30+05:00');
        $date->set(20, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T04:31:30+05:00');
        $date->set(40, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(0, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2000-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR_SHORT);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->set(30, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2030-02-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'-20-02-14T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_SHORT_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->set(70, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T04:31:30+05:00');
        $date->set(20, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T04:31:30+05:00');
        $date->set(40, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(0, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2000-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set(30, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2030-02-14T04:31:30+05:00');
        $date->set($d2, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'-20-02-14T23:31:30+00:00');
        $date->set($d2, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-13T23:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SWATCH);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:00+05:00');
        $date->set(0, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:00:00+05:00');
        $date->set(600, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:23:59+05:00');
        $date->set(1700, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-15T16:47:59+05:00');
        $date->setTimeZone('UTC');
        $date->set(1900, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-16T21:36:00+00:00');
        $date->set($d2, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-16T00:36:00+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set(3700, Zend_Date::SWATCH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-19T16:48:00+05:00');
        $date->set($d2, Zend_Date::SWATCH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-19T00:36:00+05:00');
        $date->setTimeZone('UTC');
        $date->set(-200, Zend_Date::SWATCH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-17T19:12:00+00:00');
        $date->set($d2, Zend_Date::SWATCH, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-17T00:36:00+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_SHORT_AM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->set(  3, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+05:00');
        $date->set( 14, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+05:00');
        $date->set(-6, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+05:00');
        $date->set($d2, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T22:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->set(  3, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+05:00');
        $date->set( 14, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+05:00');
        $date->set(-6, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+05:00');
        $date->set($d2, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T22:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_AM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->set(  3, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+05:00');
        $date->set( 14, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+05:00');
        $date->set(-6, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+05:00');
        $date->set($d2, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T22:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->set(  3, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+05:00');
        $date->set( 14, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+05:00');
        $date->set(-6, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+05:00');
        $date->set($d2, Zend_Date::HOUR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T22:31:30+00:00');
        $date->set($d2, Zend_Date::HOUR, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T04:31:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MINUTE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:30+05:00');
        $date->set(  3, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:03:30+05:00');
        $date->set( 65, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T05:05:30+05:00');
        $date->set(-6, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:54:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:30:30+00:00');
        $date->set($d2, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:36:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MINUTE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:09:30+05:00');
        $date->set($d2, Zend_Date::MINUTE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::MINUTE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T21:55:30+00:00');
        $date->set($d2, Zend_Date::MINUTE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T21:36:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MINUTE_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:30+05:00');
        $date->set(  3, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:03:30+05:00');
        $date->set( 65, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T05:05:30+05:00');
        $date->set(-6, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:54:30+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:30:30+00:00');
        $date->set($d2, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:36:30+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:09:30+05:00');
        $date->set($d2, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:30+05:00');
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T21:55:30+00:00');
        $date->set($d2, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T21:36:30+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SECOND);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:50+05:00');
        $date->set(  3, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:03+05:00');
        $date->set( 65, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:32:05+05:00');
        $date->set(-6, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:54+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:50+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::SECOND, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:09+05:00');
        $date->set($d2, Zend_Date::SECOND, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:50+05:00');
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::SECOND, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:29:55+00:00');
        $date->set($d2, Zend_Date::SECOND, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:29:50+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SECOND_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:50+05:00');
        $date->set(  3, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:03+05:00');
        $date->set( 65, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:32:05+05:00');
        $date->set(-6, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:54+05:00');
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:30+00:00');
        $date->set($d2, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:50+00:00');
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:09+05:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:50+05:00');
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:29:55+00:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:29:50+00:00');
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MILLISECOND);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);
        $date->set(  3, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),3);
        $date->set( 1065, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),65);
        $date->set(-6, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),994);
        $date->set( 30, Zend_Date::MILLISECOND, true);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),30);
        $date->set($d2, Zend_Date::MILLISECOND, true);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);
        $date->set( 9, Zend_Date::MILLISECOND, false, 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),9);
        $date->set($d2, Zend_Date::MILLISECOND, false, 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);
        $date->set(-65, Zend_Date::MILLISECOND, true , 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),935);
        $date->set($d2, Zend_Date::MILLISECOND, true , 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ISO_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('2007-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('2007-10-20 201030', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('07-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('80-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1980-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('-0007-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('-07-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('2007-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('2007-10-20T201030', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('20-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('80-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1980-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('-0007-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('-07-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('20071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('201020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('801020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1980-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('-071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('-00071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('20071020T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('-00071020T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T20:10:30+05:00');
        $date->set(1234567890);
        $date->set('2007-10-20', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T00:00:00+05:00');
        $date->set(1234567890);
        $date->set('20071020', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T00:00:00+05:00');
        $date->set(1234567890);
        $date->set('20071020122030', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T12:20:30+05:00');
        $date->set(1234567890);
        $date->set('071020', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T00:00:00+05:00');
        $date->set(1234567890);
        $date->set('07:10:20', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-01-01T07:10:20+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_2822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Jan 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Feb 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Mar 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Apr 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 May 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-05-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Jun 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-06-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Jul 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-07-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Aug 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Sep 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-09-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Oct 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-10-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Nov 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-11-05T01:31:30+05:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Dec 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-05T01:31:30+05:00');
        $date->set(1234567890);
        try {
            $date->set('Thu, 05 Fxx 2009 01:31:30 +0500', Zend_Date::RFC_2822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMESTAMP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('1010101099', Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:38:19+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATES);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:31:30+05:00');
        $date->set(1234567890);
        $date->set('14.02.2009', Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_FULL);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:31:30+05:00');
        $date->set(1234567890);
        $date->set('Samstag, 14. Februar 2009', Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_LONG);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:31:30+05:00');
        $date->set(1234567890);
        $date->set('14. Februar 2009', Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_MEDIUM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:31:30+05:00');
        $date->set(1234567890);
        $date->set('14.02.2009', Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:31:30+05:00');
        $date->set(1234567890);
        $date->set('14.02.09', Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMES);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('15:26:40', Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:40+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_FULL);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:00+05:00');
        $date->set(1234567890);
        $date->set('15:26 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:00+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_LONG);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('15:26:40 CET', Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:40+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_MEDIUM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('15:26:40', Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:40+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:36:00+05:00');
        $date->set(1234567890);
        $date->set('15:26', Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:00+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ATOM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+05:00', Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::COOKIE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('Saturday, 14-Feb-09 00:31:30 Europe/Vienna', Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 09 00:31:30 +0500', Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_850);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('Saturday, 14-Feb-09 00:31:30 Europe/Vienna', Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_1036);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 09 00:31:30 +0500', Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_1123);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 2009 00:31:30 +0500', Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_3339);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_3339);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+05:00', Zend_Date::RFC_3339);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RSS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 2009 00:31:30 +0500', Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::W3C);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+05:00', Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');
        // timezone will not be set by date setting, so -5 is ignored and +5 timezone is used instead
        $date->set('2009-02-14T00:31:30-05:00', Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+05:00');

        $date->set(1234567890);
        try {
            $date->set('noday', 'xx');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set($d2, 'xx');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set(1234567890);
        $date->set('1000', 'xx');
        $this->assertSame($date->get(Zend_Date::W3C),'1970-01-01T05:16:40+05:00');
    }

    /**
     * Test for add
     */
    public function testAdd()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame((string)$retour,'1234567890');
        $this->assertSame((string)$date->add(10),'1234567900');
        $this->assertSame((string)$date->add(-10),'1234567890');
        $this->assertSame((string)$date->add(0),'1234567890');

        $date->set($d2);
        $date->add(10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T04:36:50+05:00');
        $date->add(-10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T04:36:50+05:00');
        $date->add(-10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add('Montag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T04:36:50+05:00');

        $date->set($d2);
        $date->add(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }


    /**
     * Test for add
     */
    public function testAdd2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $date->set($d2);
        $date->add(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T04:36:50+05:00');
        $date->add(-10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T04:36:50+05:00');

        $date->set($d2);
        $date->add('Mo', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-03-15T04:36:50+05:00');
        $date->add(-10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add('April', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-08-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-04T04:36:50+05:00');
        $date->add(-10, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add('Apr', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-08-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-04T04:36:50+05:00');
        $date->add(-10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('M', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-06-04T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T04:36:50+05:00');
        $date->add(-10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T04:36:50+05:00');
        $date->add(-10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T04:36:50+05:00');
        $date->add(-10, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T04:36:50+05:00');
        $date->add(-10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:51:14+05:00');
        $date->add(-10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:49+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->add(-10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->add(-10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->add(-10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->add(-10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:46:50+05:00');
        $date->add(-10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:46:50+05:00');
        $date->add(-10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:37:00+05:00');
        $date->add(-10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:37:00+05:00');
        $date->add(-10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),10);
        $date->add(-10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('1000-01-02 20:05:12', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T19:42:02+05:00');

        $date->set($d2);
        $date->add('Thu, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T19:42:02+05:00');

        $date->set($d2);
        $date->add(10, Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:37:00+05:00');

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('10.02.0005', Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T04:36:50+05:00');

        $date->set($d2);
        $date->add('Samstag, 10. Februar 0005', Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T04:36:50+05:00');

        $date->set($d2);
        $date->add('10. Februar 0005', Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T04:36:50+05:00');

        $date->set($d2);
        $date->add('10.02.0005', Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T04:36:50+05:00');

        $date->set($d2);
        $date->add('10.02.05', Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'4007-03-14T04:36:50+05:00');

        $date->set($d2);
        $date->add('10:05:05', Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:41:55+05:00');

        $date->set($d2);
        $date->add('10:05 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:41:50+05:00');

        $date->set($d2);
        $date->add('10:05:05 CET', Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:41:55+05:00');

        $date->set($d2);
        $date->add('10:05:05', Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:41:55+05:00');

        $date->set($d2);
        $date->add('10:05', Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:41:50+05:00');

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+05:00', Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-08T00:42:02+05:00');

        $date->set($d2);
        $date->add('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-07T00:42:02+05:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-06T19:42:02+05:00');

        $date->set($d2);
        $date->add('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-07T00:42:02+05:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-07T00:42:02+05:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-08T00:42:02+05:00');

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+05:00', Zend_Date::RFC_3339);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-08T00:42:02+05:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-08T00:42:02+05:00');

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+05:00', Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-08T00:42:02+05:00');

        $date->set($d2);
        $date->add('1000', 'xx');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:53:30+05:00');
    }

    /**
     * Test for sub
     */
    public function testSub()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame((string)$retour,'1234567890');
        $this->assertSame((string)$date->sub(-10),'1234567900');
        $this->assertSame((string)$date->sub(10),'1234567890');
        $this->assertSame((string)$date->sub(0),'1234567890');

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T04:36:50+05:00');
        $date->sub(10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');
    }

    /**
     * Test for sub
     */
    public function testSub2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $date->set($d2);
        $date->sub('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T04:36:50+05:00');
        $date->sub(10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub('Montag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T04:36:50+05:00');

        $date->set($d2);
        $date->sub(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T04:36:50+05:00');
        $date->sub(10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T04:36:50+05:00');

        $date->set($d2);
        $date->sub('Mo', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-03-15T04:36:50+05:00');
        $date->sub(10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub('April', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2001-09-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-04T04:36:50+05:00');
        $date->sub(10, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub('Apr', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2001-09-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-04T04:36:50+05:00');
        $date->sub(10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('M', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2001-10-04T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(-10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T04:36:50+05:00');
        $date->sub(10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T04:36:50+05:00');
        $date->sub(10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(10, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'1992-01-04T04:36:50+05:00');
        $date->sub(-10, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1992-01-04T04:36:50+05:00');
        $date->sub(-10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(-10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:51:15+05:00');
        $date->sub(10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:51+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->sub(10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->sub(10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->sub(10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T14:36:50+05:00');
        $date->sub(10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:46:50+05:00');
        $date->sub(10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:46:50+05:00');
        $date->sub(10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:37:00+05:00');
        $date->sub(10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:37:00+05:00');
        $date->sub(10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:36:50+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),10);
        $date->sub(10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),0);

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('1000-01-02 20:05:12', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T13:31:38+05:00');
        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T13:31:38+05:00');

        $date->set($d2);
        $date->sub('Thu, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T13:31:38+05:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:37:00+05:00');

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('10.02.0005', Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T04:36:50+05:00');

        $date->set($d2);
        $date->sub('Samstag, 10. Februar 0005', Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T04:36:50+05:00');

        $date->set($d2);
        $date->sub('10. Februar 0005', Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T04:36:50+05:00');

        $date->set($d2);
        $date->sub('10.02.0005', Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T04:36:50+05:00');

        $date->set($d2);
        $date->sub('10.02.05', Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'-4-10-29T04:36:50+05:00');

        $date->set($d2);
        $date->sub('10:05:05', Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T18:31:45+05:00');

        $date->set($d2);
        $date->sub('10:05 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T18:31:50+05:00');

        $date->set($d2);
        $date->sub('10:05:05 CET', Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T18:31:45+05:00');

        $date->set($d2);
        $date->sub('10:05:05', Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T18:31:45+05:00');

        $date->set($d2);
        $date->sub('10:05', Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T18:31:50+05:00');

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T08:31:38+05:00');

        $date->set($d2);
        $date->sub('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T08:31:38+05:00' );

        $date->set($d2);
        $date->sub('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T13:31:38+05:00');

        $date->set($d2);
        $date->sub('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T08:31:38+05:00');

        $date->set($d2);
        $date->sub('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T08:31:38+05:00');

        $date->set($d2);
        $date->sub('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T08:31:38+05:00');

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::RFC_3339);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T08:31:38+05:00');

        $date->set($d2);
        $date->sub('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T08:31:38+05:00');

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T08:31:38+05:00');

        $date->set($d2);
        $date->sub('1000', 'xx');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T04:20:10+05:00');
    }

    /**
     * Test for compare
     */
    public function testCompare()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);//03.01.2002 15:36:50

        $retour = $date->set(1234567890); //13.02.2009 15:31:30
        $this->assertSame((string)$retour,'1234567890');
        $this->assertSame($date->compare(1234567890),0);
        $this->assertSame($date->compare(1234567800),1);
        $this->assertSame($date->compare(1234567899),-1);

        $date->set($d2);//03.01.2002 15:36:50
        $this->assertSame($date->compare(3,Zend_Date::DAY),1);
        $this->assertSame($date->compare(4,Zend_Date::DAY),0);
        $this->assertSame($date->compare(5,Zend_Date::DAY),-1);

        $this->assertSame($date->compare('Mon',Zend_Date::WEEKDAY_SHORT),1);
        $this->assertSame($date->compare('Sam',Zend_Date::WEEKDAY_SHORT),-1);

        $date->set($d2);//03.01.2002 15:36:50
        $this->assertSame($date->compare(0,Zend_Date::MILLISECOND),0);
    }

    /**
     * Test for copy
     */
    public function testCopy()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $date->set(1234567890);
        $newdate = clone $date;
        $this->assertSame($date->get(),$newdate->get());

        $date->set($d2);
        $newdate = $date->copyPart(Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C), '2002-01-04T04:36:50+05:00');
        $this->assertSame($newdate->get(Zend_Date::W3C), '1970-01-04T05:00:00+05:00');
    }

    /**
     * Test for equals
     */
    public function testEquals()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame((string)$retour,'1234567890');
        $this->assertSame($date->equals(1234567890),true);
        $this->assertSame($date->equals(1234567800),false);

        $date->set($d2);
        $this->assertSame($date->equals(3,Zend_Date::DAY),false);
        $this->assertSame($date->equals(4,Zend_Date::DAY),true);
    }

    /**
     * Test for isEarlier
     */
    public function testIsEarlier()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame((string)$retour,'1234567890');
        $this->assertSame($date->isEarlier(1234567890),false);
        $this->assertSame($date->isEarlier(1234567800),false);
        $this->assertSame($date->isEarlier(1234567899),true);

        $date->set($d2);
        $this->assertSame($date->isEarlier(3,Zend_Date::DAY),false);
        $this->assertSame($date->isEarlier(4,Zend_Date::DAY),false);
        $this->assertSame($date->isEarlier(5,Zend_Date::DAY),true);
    }

    /**
     * Test for isLater
     */
    public function testIsLater()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame((string)$retour,'1234567890');
        $this->assertSame($date->isLater(1234567890),false);
        $this->assertSame($date->isLater(1234567800),true);
        $this->assertSame($date->isLater(1234567899),false);

        $date->set($d2);
        $this->assertSame($date->isLater(3,Zend_Date::DAY),true);
        $this->assertSame($date->isLater(4,Zend_Date::DAY),false);
        $this->assertSame($date->isLater(5,Zend_Date::DAY),false);
    }

    /**
     * Test for getTime
     */
    public function testGetTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $result = $date->getTime();
        $this->assertSame($result->get(Zend_Date::W3C),'1970-01-01T04:36:50+05:00');
    }

    /**
     * Test for setTime
     */
    public function testSetTime()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->setTime(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setTime('10:20:30');
        $this->assertSame($result->get(Zend_Date::W3C),'2009-02-14T10:20:30+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T10:20:30+05:00');
        $date->setTime('30-20-10','ss:mm:HH');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T10:20:30+05:00');
        $date->setTime($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:39+05:00');

        $date = new Zend_Date(Zend_Date::now(), $locale);
        $t1 = $date->get(Zend_Date::TIMESTAMP);
        $date->setTime(Zend_Date::now());
        $t2 = $date->get(Zend_Date::TIMESTAMP);
        $diff = abs($t2 - $t1);
        $this->assertTrue($diff < 2, "Instance of Zend_Date has a significantly different time than returned by setTime(): $diff seconds");
    }

    /**
     * Test for addTime
     */
    public function testAddTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->addTime(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->addTime('10:20:30');
        $this->assertSame($result->get(Zend_Date::W3C),'2009-02-14T14:52:00+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:52:00+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addTime('30:20:10','ss:mm:HH');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:52:00+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addTime($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:03:09+05:00');
    }

    /**
     * Test for subTime
     */
    public function testSubTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->subTime(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->subTime('10:20:30');
        $this->assertSame($result->get(Zend_Date::W3C),'2009-02-13T18:11:00+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:11:00+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subTime('30-20-10','ss:mm:HH');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:11:00+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subTime($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:59:51+05:00');
    }

    /**
     * Test for compareTime
     */
    public function testCompareTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $date = new Zend_Date(1234567890,null,$locale);
        $this->assertSame($date->compareTime('10:20:30'), -1);
        $this->assertSame($date->compareTime('04:31:30'), 0);
        $this->assertSame($date->compareTime('04:00:30'), 1);
        $this->assertSame($date->compareTime($d2), -1);
    }

    /**
     * Test for setTime
     */
    public function testSetHour()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,null,$locale);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
        for($i=23; $i >= 0; $i--) {
            $date->setHour($i);
            $hour = $i;
            if ($i < 10) {
                $hour = '0' . $hour;
            }
            $this->assertSame($date->get(Zend_Date::W3C),"2009-02-14T$hour:31:30+05:00");
        }
    }

    /**
     * Test for getDate
     */
    public function testGetDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $result = $date->getDate();
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T05:00:00+05:00');
    }

    /**
     * Test for setDate
     */
    public function testSetDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->setDate(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setDate('11.05.2008');
        // Hint: the hour changes from 0 to 1 because of DST...
        // An hour is added by winter->summertime change
        $this->assertSame($result->get(Zend_Date::W3C),'2008-05-11T04:31:30+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-05-11T04:31:30+05:00');
        $date->setDate('2008-05-11','YYYY-MM-dd');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-05-11T04:31:30+05:00');
        $date->setDate($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:30+05:00');
    }

    /**
     * Test for addDate
     */
    public function testAddDate()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->addDate(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->addDate('02-03-05');
        $this->assertSame($result->get(Zend_Date::W3C),'2014-05-17T04:31:30+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2014-05-17T04:31:30+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addDate('05-03-02','YY-MM-dd');
        $this->assertSame($date->get(Zend_Date::W3C),'2014-05-17T04:31:30+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addDate($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'4018-04-28T04:31:30+05:00');
    }

    /**
     * Test for subDate
     */
    public function testSubDate()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->subDate(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->subDate('03-05-1001');
        $this->assertSame($result->get(Zend_Date::W3C),'1007-09-08T04:31:30+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'1007-09-08T04:31:30+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subDate('1001-05-03','YYYY-MM-dd');
        $this->assertSame($date->get(Zend_Date::W3C),'1007-09-08T04:31:30+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subDate($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'-1-12-06T04:31:30+05:00');
    }

    /**
     * Test for compareDate
     */
    public function testCompareDate()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame($date->compareDate('10.01.2009'), 1);
        $this->assertSame($date->compareDate('14.02.2009'), 0);
        $this->assertSame($date->compareDate('15.02.2009'), -1);
        $this->assertSame($date->compareDate($d2), 0);
    }

    /**
     * Test for getIso
     */
    public function testGetIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $result = $date->getIso();
        $this->assertTrue(is_string($result));
        $this->assertSame($result,'2002-01-04T04:36:50+05:00');
    }

    /**
     * Test for setIso
     */
    public function testSetIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->setIso(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setIso('2002-01-04T00:00:00+0000');
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T00:00:00+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:00:00+05:00');
        $date->setIso($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:39+05:00');
    }

    /**
     * Test for addIso
     */
    public function testAddIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->addIso(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
    }

    /**
     * Test for addIso
     */
    public function testAddIso2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->setIso('2002-01-04T01:00:00+0500');
        $result = $date->addIso('0000-00-00T01:00:00+0500');
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-03T21:00:00+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T21:00:00+05:00');

        $date->addIso('0001-01-01T01:01:01+0500');
        $this->assertSame($date->get(Zend_Date::W3C),'2003-02-04T17:01:01+05:00');

        $date = new Zend_Date(1234567890,$locale);
        $date->addIso($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'4018-04-28T04:03:09+05:00');
    }

    /**
     * Test for subIso
     */
    public function testSubIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->subIso(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
    }

    /**
     * Test for subIso
     */
    public function testSubIso2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);
        $result = $date->subIso('0000-00-00T01:00:00+0500');
        $this->assertSame($result->get(Zend_Date::W3C),'2009-02-14T08:31:30+05:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T08:31:30+05:00');

        $result = $date->subIso('0001-01-01T01:01:01+0500');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-01-14T12:30:29+05:00');

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subIso($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'-1-12-06T04:59:51+05:00');
    }

    /**
     * Test for compareIso
     */
    public function testCompareIso()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $date = new Zend_Date(1234567890,null,$locale);
        $this->assertSame($date->compareIso('2002-01-04T04:00:00+0500'), 1);
        $this->assertSame($date->compareIso('2009-02-14T04:31:30+0500'), 0);
        $this->assertSame($date->compareIso('2010-01-04T05:00:00+0500'), -1);
        $this->assertSame($date->compareIso($d2), -1);
    }

    /**
     * Test for getArpa
     */
    public function testGetArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);

        $result = $date->getArpa();
        $this->assertTrue(is_string($result));
        $this->assertSame($result,'Fri, 04 Jan 02 04:36:50 +0500');
    }

    /**
     * Test for setArpa
     */
    public function testSetArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);
        $date->setTimezone('Indian/Maldives');

        $result = $date->setArpa(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setArpa('Sat, 03 May 01 00:00:00 +0500');
        $this->assertSame($result->get(Zend_Date::RFC_822),'Thu, 03 May 01 00:00:00 +0500');
        $this->assertSame($date->get(Zend_Date::W3C),'2001-05-03T00:00:00+05:00');
        $date->setArpa($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T04:31:39+05:00');
    }

    /**
     * Test for addArpa
     */
    public function testAddArpa()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->addArpa(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,$locale);
        $result = $date->addArpa('Sat, 03 May 01 00:00:00 +0500');
        $this->assertSame($result->get(Zend_Date::RFC_822),'Sat, 17 Jul 10 23:31:30 +0500');
        $this->assertSame($date->get(Zend_Date::W3C),'4010-07-17T23:31:30+05:00');

        $date = new Zend_Date(1234567890,$locale);
        $date->addArpa($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'4018-04-28T04:03:09+05:00');

        $result = $date->setArpa('Fri, 05 Jan 07 03:35:53 +0500');
        $arpa = $result->getArpa();
        $this->assertSame($arpa,'Fri, 05 Jan 07 03:35:53 +0500');
    }

    /**
     * Test for subArpa
     */
    public function testSubArpa()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->subArpa(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->subArpa('Sat, 03 May 01 00:00:00 +0500');
        $this->assertSame($result->get(Zend_Date::RFC_822),'Wed, 16 Sep 7 09:31:30 +0500');
        $this->assertSame($date->get(Zend_Date::W3C),'7-09-16T09:31:30+05:00');

        $date = new Zend_Date(1234567890,$locale);
        $date->subArpa($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'-1-12-06T04:59:51+05:00');
    }

    /**
     * Test for compareArpa
     */
    public function testCompareArpa()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame($date->compareArpa('Sat, 14 Feb 09 05:31:30 +0500'), -1);
        $this->assertSame($date->compareArpa('Sat, 14 Feb 09 04:31:30 +0500'), 0);
        $this->assertSame($date->compareArpa('Sat, 13 Feb 09 04:31:30 +0500'), 1);
        $this->assertSame($date->compareArpa($d2), -1);
    }

    /**
     * Test for false locale setting
     */
    public function testReducedParams()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);

        $date->setArpa('Sat, 03 May 01 00:00:00 +0500',$locale);
        $this->assertSame($date->get(Zend_Date::RFC_822),'Thu, 03 May 01 00:00:00 +0500');
    }

    /**
     * Test for SunFunc
     */
    public function testSunFunc()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);
        $date->setTimezone(date_default_timezone_get());

        $result = Zend_Date_Cities::City('vienna');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T20:09:59+05:00');

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'civil');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T20:09:20+05:00');

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'nautic');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T20:08:34+05:00');

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'astronomic');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T20:07:49+05:00');

        unset($result);
        $result = Zend_Date_Cities::City('BERLIN');
        $this->assertTrue(is_array($result));
        $result = $date->getSunrise($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T12:21:21+05:00');

        unset($result);
        $result = Zend_Date_Cities::City('London');
        $this->assertTrue(is_array($result));
        $result = $date->getSunInfo($result);
        $this->assertSame($result['sunrise']['effective']->get(Zend_Date::W3C), '2002-01-04T13:10:10+05:00');
        $this->assertSame($result['sunrise']['civil']->get(Zend_Date::W3C),     '2002-01-04T13:10:54+05:00');
        $this->assertSame($result['sunrise']['nautic']->get(Zend_Date::W3C),    '2002-01-04T13:11:45+05:00');
        $this->assertSame($result['sunrise']['astronomic']->get(Zend_Date::W3C),'2002-01-04T13:12:35+05:00');
        $this->assertSame($result['sunset']['effective']->get(Zend_Date::W3C),  '2002-01-04T21:00:52+05:00');
        $this->assertSame($result['sunset']['civil']->get(Zend_Date::W3C),      '2002-01-04T21:00:08+05:00');
        $this->assertSame($result['sunset']['nautic']->get(Zend_Date::W3C),     '2002-01-04T20:59:18+05:00');
        $this->assertSame($result['sunset']['astronomic']->get(Zend_Date::W3C), '2002-01-04T20:58:28+05:00');

        unset($result);
        $result = array('longitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('latitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => 180.1, 'latitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => -180.1, 'latitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => 0, 'latitude' => 90.1);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => 0, 'latitude' => -90.1);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('latitude' => 0, 'longitude' => 0);
        $result = $date->getSunInfo($result);
        $this->assertTrue(is_array($result));

        unset($result);
        $result = array('latitude' => 0, 'longitude' => 0);
        $result = $date->getSunrise($result);
        $this->assertTrue($result instanceof Zend_Date);
    }

    /**
     * Test for Timezone
     */
    public function testTimezone()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);
        $date->setTimezone(date_default_timezone_get());

        $result = $date->getTimezone();
        $this->assertSame($result, 'Indian/Maldives');

        try {
            $result = $date->setTimezone('unknown');
            // if function timezone_identifiers_list is not avaiable false should be returned
            $this->assertSame($result, false);
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $result = $date->getTimezone();
        $this->assertSame($result, 'Indian/Maldives');

        $result = $date->setTimezone('America/Chicago');
        $this->assertSame($result, true);
        $result = $date->getTimezone();
        $this->assertSame($result, 'America/Chicago');
    }

    /**
     * Test for LeapYear
     */
    public function testLeapYear()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date('01.01.2006', Zend_Date::DATES, $locale);
        $this->assertFalse($date->isLeapYear());

        unset($date);
        $date = new Zend_Date('01.01.2004', Zend_Date::DATES, $locale);
        $this->assertTrue($date->isLeapYear());

        try {
            $result = Zend_Date::checkLeapYear('noyear');
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // succeed
        }
    }

    /**
     * Test for Today
     */
    public function testToday()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(Zend_Date::now());
        $d2 = new Zend_Date(1010101010,$locale);

        $this->assertFalse($d2->isToday());
        $this->assertTrue($date->isToday());
    }

    /**
     * Test for Yesterday
     */
    public function testYesterday()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(Zend_Date::now());
        $d2 = new Zend_Date(1010101010,$locale);
        $date->subDay(1);
        $this->assertFalse($d2->isYesterday());
        $this->assertTrue($date->isYesterday());
    }

    /**
     * Test for Tomorrow
     */
    public function testTomorrow()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(Zend_Date::now());
        $d2 = new Zend_Date(1010101010,$locale);

        $date->addDay(1);
        $this->assertFalse($d2->isTomorrow());
        $this->assertTrue($date->isTomorrow());
    }

    /**
     * test isToday(), isTomorrow(), and isYesterday() for cases other than time() = "now"
     */
    public function testIsDay()
    {
        date_default_timezone_set('Europe/Vienna'); // should have DST
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date_TestHelper('01.01.2006', Zend_Date::DATES, $locale);

        $date->_setTime($date->mktime(0, 0, 0, 1, 1, 2006));
        $this->assertTrue($date->isToday());
        $this->assertFalse($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 1, 1, 2006));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2005));
        $this->assertTrue($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2005));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2006));
        $this->assertFalse($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2006));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 1, 0, 2006));
        $this->assertTrue($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 1, 0, 2006));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 1, 2, 2006));
        $this->assertFalse($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 1, 2, 2006));
        $this->assertTrue($date->isYesterday());
    }

    /**
     * Test for Now
     */
    public function testNow()
    {
        $locale = new Zend_Locale('de_AT');

        $date = Zend_Date::now();

        $reference = date('U');
        $this->assertTrue(($reference - $date->get(Zend_Date::TIMESTAMP)) < 2);
    }

    /**
     * Test for getYear
     */
    public function testGetYear()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,$locale);
        $d2 = new Zend_Date(1610101010,$locale);
        $date->setTimeZone(date_default_timezone_get());
        $d2->setTimeZone(date_default_timezone_get());

        $result = $date->getYear();
        $this->assertTrue($result instanceof Zend_Date);
        $this->assertSame($result->toString(), '01.01.2009 05:00:00');
        $this->assertSame($d2->getYear()->toString(), '01.01.2021 05:00:00');
    }

    /**
     * Test for setYear
     */
    public function testSetYear()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date2 = new Zend_Date(2006, Zend_Date::YEAR);
        $date->setTimeZone(date_default_timezone_get());

        $date->setYear(2000);
        $this->assertSame($date->get(Zend_Date::W3C), '2000-01-01T04:00:00+05:00');

        $date->setYear(1800);
        $this->assertSame($date->get(Zend_Date::W3C), '1800-01-01T04:00:00+05:00');

        $date->setYear(2100);
        $this->assertSame($date->get(Zend_Date::W3C), '2100-01-01T04:00:00+05:00');

        $date->setYear($date2);
        $this->assertSame($date->get(Zend_Date::W3C), '2006-01-01T04:00:00+05:00');

        try {
            $date->setYear('noyear');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for addYear
     */
    public function testAddYear()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2021-01-01T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2022-01-01T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2023-01-01T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2024-01-01T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2025-01-01T04:00:00+05:00');

        $date->setYear(1500);
        $this->assertSame($date->get(Zend_Date::W3C), '1500-01-01T04:00:00+05:00');
        $date->addYear(20);
        $this->assertSame($date->get(Zend_Date::W3C), '1520-01-01T04:00:00+05:00');

        $date->setYear(2100);
        $this->assertSame($date->get(Zend_Date::W3C), '2100-01-01T04:00:00+05:00');
        $date->addYear(20);
        $this->assertSame($date->get(Zend_Date::W3C), '2120-01-01T04:00:00+05:00');

        $date->setDay(4);
        $date->setMonth(4);
        $date->setYear(2020);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-04-04T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2021-04-04T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2022-04-04T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2023-04-04T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2024-04-04T04:00:00+05:00');
        $date->addYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2025-04-04T04:00:00+05:00');

        $date->setYear(1500);
        $this->assertSame($date->get(Zend_Date::W3C), '1500-04-04T04:00:00+05:00');
        $date->addYear(20);
        $this->assertSame($date->get(Zend_Date::W3C), '1520-04-04T04:00:00+05:00');

        $date->setYear(2100);
        $this->assertSame($date->get(Zend_Date::W3C), '2100-04-04T04:00:00+05:00');
        $date->addYear(20);
        $this->assertSame($date->get(Zend_Date::W3C), '2120-04-04T04:00:00+05:00');
    }

    /**
     * Test for subYear
     */
    public function testSubYear()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->subYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2019-01-01T04:00:00+05:00');
        $date->subYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2018-01-01T04:00:00+05:00');
        $date->subYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2017-01-01T04:00:00+05:00');
        $date->subYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2016-01-01T04:00:00+05:00');
        $date->subYear(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2015-01-01T04:00:00+05:00');

        $date->setYear(1500);
        $this->assertSame($date->get(Zend_Date::W3C), '1500-01-01T04:00:00+05:00');
        $date->subYear(20);
        $this->assertSame($date->get(Zend_Date::W3C), '1480-01-01T04:00:00+05:00');

        $date->setYear(2100);
        $this->assertSame($date->get(Zend_Date::W3C), '2100-01-01T04:00:00+05:00');
        $date->subYear(20);
        $this->assertSame($date->get(Zend_Date::W3C), '2080-01-01T04:00:00+05:00');
    }

    /**
     * Test for compareYear
     */
    public function testCompareYear()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame($date->compareYear(2010), -1);
        $this->assertSame($date->compareYear(2009), 0);
        $this->assertSame($date->compareYear(2008), 1);
        $this->assertSame($date->compareYear($d2), 0);
    }

    /**
     * Test for getMonth
     */
    public function testGetMonth()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,$locale);
        $d2 = new Zend_Date(1610101010,$locale);
        $date->setTimeZone(date_default_timezone_get());
        $d2->setTimeZone(date_default_timezone_get());

        $result = $date->getMonth();
        $this->assertTrue($result instanceof Zend_Date);
        $this->assertSame($result->toString(), '01.02.1970 05:00:00');
        $this->assertSame($date->getMonth()->toString(), '01.02.1970 05:00:00');
    }

    /**
     * Test for setMonth
     */
    public function testSetMonth()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date2 = new Zend_Date(2006, Zend_Date::YEAR);
        $date->setTimeZone(date_default_timezone_get());

        $date->setMonth(3);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-03-01T04:00:00+05:00');

        $date->setMonth(-3);
        $this->assertSame($date->get(Zend_Date::W3C), '2019-09-01T04:00:00+05:00');

        $date->setMonth('March', 'en');
        $this->assertSame($date->get(Zend_Date::W3C), '2019-03-01T04:00:00+05:00');

        $date->setMonth($date2);
        $this->assertSame($date->get(Zend_Date::W3C), '2019-01-01T04:00:00+05:00');

        try {
            $date->setMonth('nomonth');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for addMonth
     */
    public function testAddMonth()
    {
        date_default_timezone_set('Europe/Vienna');
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->addMonth(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-02-01T00:00:00+01:00');
        $date->addMonth(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-03-01T00:00:00+01:00');
        $date->addMonth(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-04-01T00:00:00+02:00');
        $date->addMonth(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-05-01T00:00:00+02:00');
        $date->addMonth(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-06-01T00:00:00+02:00');
        $date->addMonth(5);
        $this->assertSame($date->get(Zend_Date::W3C), '2020-11-01T00:00:00+01:00');

        Zend_Date::setOptions(array('fix_dst' => true));
        $date = new Zend_Date('2007-10-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-10-01 00:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-11-01 00:00:00');

        $date = new Zend_Date('2007-10-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-10-01 23:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-11-01 23:00:00');

        $date = new Zend_Date('2007-03-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-03-01 00:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-04-01 00:00:00');

        $date = new Zend_Date('2007-03-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-03-01 23:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-04-01 23:00:00');

        Zend_Date::setOptions(array('fix_dst' => false));
        $date = new Zend_Date('2007-10-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-10-01 00:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-10-31 23:00:00');

        $date = new Zend_Date('2007-10-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-10-01 23:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-11-01 22:00:00');

        $date = new Zend_Date('2007-03-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-03-01 00:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-04-01 01:00:00');

        $date = new Zend_Date('2007-03-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-03-01 23:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-04-02 00:00:00');

        $date = new Zend_Date('2007-01-31 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-01-31 00:00:00');
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-02-28 00:00:00');

        $date = new Zend_Date('2007-01-31 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-01-31 00:00:00');
        Zend_Date::setOptions(array('extend_month' => true));
        $date->addMonth(1);
        $this->assertSame($date->toString('yyyy-MM-dd HH:mm:ss'), '2007-03-03 00:00:00');

        date_default_timezone_set('America/Chicago');
        $date = new Zend_Date(1577858400,$locale);
        $date->setTimeZone(date_default_timezone_get());
        $this->assertSame($date->get(Zend_Date::ISO_8601), '2020-01-01T00:00:00-06:00');
        $date->addMonth(12);
        $this->assertSame($date->get(Zend_Date::ISO_8601), '2021-01-01T00:00:00-06:00');

    }

    /**
     * Test for subMonth
     */
    public function testSubMonth()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->subMonth(1);
        $this->assertSame($date->get(Zend_Date::W3C), '2019-12-01T04:00:00+05:00');
        $date->subMonth(12);
        $this->assertSame($date->get(Zend_Date::W3C), '2018-12-01T04:00:00+05:00');
    }

    /**
     * Test for compareMonth
     */
    public function testCompareMonth()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame($date->compareMonth(1), 1);
        $this->assertSame($date->compareMonth(2), 0);
        $this->assertSame($date->compareMonth(3), -1);
        $this->assertSame($date->compareYear($d2), 0);
    }

    /**
     * Test accessors for _Locale member property of Zend_Date
     */
    public function testLocale()
    {
        $date = new Zend_Date(Zend_Date::now());
        $locale = new Zend_Locale('en_Us');
        $set = $date->setLocale($locale);
        $this->assertSame($date->getLocale(),$set);
    }

    /**
     * test for getWeek
     */
    public function testGetWeek()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1168293600, $locale);

        //Tuesday
        $date->addDay(1);
        $this->assertSame($date->getWeek()->toString(),'08.01.1970 05:00:00');

        //Wednesday
        $date->addDay(1);
        $this->assertSame($date->getWeek()->toString(),'08.01.1970 05:00:00');

        //Thursday
        $date->addDay(1);
        $this->assertSame($date->getWeek()->toString(),'08.01.1970 05:00:00');

        //Friday
        $date->addDay(1);
        $this->assertSame($date->getWeek()->toString(),'08.01.1970 05:00:00');

        //Friday 05:30 am
        $date->addTime('05:30:00');
        $this->assertSame($date->getWeek()->toString(),'08.01.1970 05:00:00');

        //Saturday
        $date->addDay(1);
        $this->assertSame($date->getWeek()->toString(),'08.01.1970 05:00:00');

        //Saturday [ar_EG]
        // The right value for AM/PM has to be set in arabic letters
        $this->assertSame($date->getWeek('ar_EG')->toString(), '08/01/1970 5:00:00 ص');
        $date->setTimeZone('UTC');
        $this->assertSame($date->getWeek('ar_EG')->toString(), '08/01/1970 12:00:00 ص');
        $date->setTimeZone('Indian/Maldives');
        $this->assertSame($date->getWeek('ar_EG')->toString(), '08/01/1970 5:00:00 ص');

        //Sunday [start of a new week as defined per ISO 8601]
        $date->addDay(1);
        $this->assertSame($date->getWeek()->toString(),'15.01.1970 05:00:00');

        //Monday
        $date->addDay(1);
        $this->assertSame($date->getWeek()->toString(),'15.01.1970 05:00:00');

        //Monday 03:45 pm
        $date->addTime('15:45:00');
        $this->assertSame($date->getWeek()->toString(),'15.01.1970 05:00:00');
    }

    /**
     * test setting dates to specify weekdays
     */
    public function testDay()
    {
        // all tests and calculations below are in GMT (that is intention for this test)
        $date = new Zend_Date(0, 'de_AT');
        $date->setTimeZone('UTC');
        $dw = $date->getDay();
        $this->assertSame($dw->toString(), '01.01.1970 00:00:00');
        for($day = 1; $day < 31; $day++) {
            $date->setDay($day);
            $dw = $date->getDay();
            $weekday = str_pad($day, 2, '0', STR_PAD_LEFT);
            $this->assertSame($dw->toString(), "$weekday.01.1970 00:00:00");
        }
    }

    /**
     * test setLocale/getLocale
     */
    public function testSetLocale()
    {
        $date = new Zend_Date(0, 'de');

        $this->assertSame($date->getLocale(), 'de');
        $date->setLocale('en');
        $this->assertSame($date->getLocale(), 'en');
        $date->setLocale('en_XX');
        $this->assertSame($date->getLocale(), 'en');
        $date->setLocale('de_AT');
        $this->assertSame($date->getLocale(), 'de_AT');
        $locale = new Zend_Locale('ar');
        $date->setLocale($locale);
        $this->assertSame($date->getLocale(), 'ar');

        try {
            $date->setLocale('xx_XX');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * test looseBehaviour
     */
    public function testLoose()
    {
        $date = new Zend_Date(0, 'de');

        try {
            $date->set(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(10, 'de');
        $this->assertEquals($date->getTimestamp(), 10);

        try {
            $date->add(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->add(10, 'de');
        $this->assertEquals($date->getTimestamp(), 20);

        try {
            $date->sub(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->sub(10, 'de');
        $this->assertEquals($date->getTimestamp(), 10);

        try {
            $date->compare(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->equals(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->isEarlier(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->isLater(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    public function testTimesync()
    {
        // @todo: when the Zend_TimeSync adapter moves out of the incubator,
        // the following hack to allow it to be loaded should be removed.
        // see also ZF-954
        $incubator = dirname(dirname(dirname(__FILE__)))
            . DIRECTORY_SEPARATOR . 'incubator' . DIRECTORY_SEPARATOR . 'library';
        $include_path = get_include_path();
        set_include_path($include_path . PATH_SEPARATOR . $incubator);
        try {
            Zend_Loader::loadClass('Zend_TimeSync');
            Zend_Loader::loadClass('Zend_TimeSync_Ntp');
        } catch (Zend_Exception $e) {
            $this->markTestIncomplete($e->getMessage());
        }
        set_include_path($include_path);
        // @todo: end of hack

        try {
            $server = new Zend_TimeSync('ntp://pool.ntp.org', 'alias');
            $date1 = $server->getDate();
            // need to use the proxy class to simulate time() returning wrong value
            $date2 = new Zend_Date_TestHelper(time());

            $info = $server->getInfo();

            if ($info['offset'] != 0) {
                $this->assertFalse($date1->getTimestamp() == $date2->getTimestamp());
            } else {
                $this->assertSame($date1->getTimestamp(), $date2->getTimestamp());
            }
        } catch (Zend_TimeSync_Exception $e) {
            $this->markTestIncomplete('NTP timeserver not available.');
        }
    }

    public function testUsePhpDateFormat()
    {
        Zend_Date::setOptions(array('format_type' => 'iso'));

        // PHP date() format specifier tests
        $date1 = new Zend_Date('2006-01-02 23:58:59', Zend_Date::ISO_8601, 'en_US');
        $date2 = new Zend_Date('2006-01-02 23:58:59', 'YYYY-MM-dd HH:mm:ss', 'en_US');
        $this->assertSame($date1->getTimestamp(), $date2->getTimestamp());

        date_default_timezone_set('GMT');
        $date = new Zend_Date(0); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame($date->toString('eee'), gmdate('w',$date->getTimestamp()));
        $this->assertSame($date->toString('dd'), gmdate('d',$date->getTimestamp()));
        $this->assertSame($date->toString('EEE', 'en'), gmdate('D',$date->getTimestamp()));
        $this->assertSame($date->toString('d'), gmdate('j',$date->getTimestamp()));
        $this->assertSame($date->toString('EEEE', 'en'), gmdate('l',$date->getTimestamp()));
        $this->assertSame($date->toString('e'), gmdate('N',$date->getTimestamp()));
        $this->assertSame($date->toString('SS'), gmdate('S',$date->getTimestamp()));
        $this->assertSame($date->toString('D'), gmdate('z',$date->getTimestamp()));
        $this->assertSame($date->toString('w'), gmdate('W',$date->getTimestamp()));
        $this->assertSame($date->toString('MMMM', 'en'), gmdate('F',$date->getTimestamp()));
        $this->assertSame($date->toString('MM'), gmdate('m',$date->getTimestamp()));
        $this->assertSame($date->toString('MMM', 'en'), gmdate('M',$date->getTimestamp()));
        $this->assertSame($date->toString('M'), gmdate('n',$date->getTimestamp()));
        $this->assertSame($date->toString('ddd'), gmdate('t',$date->getTimestamp()));
        $this->assertSame($date->toString('l'), gmdate('L',$date->getTimestamp()));
        $this->assertSame($date->toString('YYYY'), gmdate('o',$date->getTimestamp()));
        $this->assertSame($date->toString('yyyy'), gmdate('Y',$date->getTimestamp()));
        $this->assertSame($date->toString('yy'), gmdate('y',$date->getTimestamp()));
        $this->assertSame(strtolower($date->toString('a', 'en')), gmdate('a',$date->getTimestamp()));
        $this->assertSame(strtoupper($date->toString('a', 'en')), gmdate('A',$date->getTimestamp()));
        $this->assertSame($date->toString('B'), gmdate('B',$date->getTimestamp()));
        $this->assertSame($date->toString('h'), gmdate('g',$date->getTimestamp()));
        $this->assertSame($date->toString('H'), gmdate('G',$date->getTimestamp()));
        $this->assertSame($date->toString('hh'), gmdate('h',$date->getTimestamp()));
        $this->assertSame($date->toString('HH'), gmdate('H',$date->getTimestamp()));
        $this->assertSame($date->toString('mm'), gmdate('i',$date->getTimestamp()));
        $this->assertSame($date->toString('ss'), gmdate('s',$date->getTimestamp()));
        $this->assertSame($date->toString('zzzz'), date('e',$date->getTimestamp()));
        $this->assertSame($date->toString('I'), gmdate('I',$date->getTimestamp()));
        $this->assertSame($date->toString('Z'), gmdate('O',$date->getTimestamp()));
        $this->assertSame($date->toString('ZZZZ'), gmdate('P',$date->getTimestamp()));
        $this->assertSame($date->toString('z'), gmdate('T',$date->getTimestamp()));
        $this->assertSame($date->toString('X'), gmdate('Z',$date->getTimestamp()));
        $this->assertSame($date->toString('yyyy-MM-ddTHH:mm:ssZZZZ'), gmdate('c',$date->getTimestamp()));
        $this->assertSame($date->toString('r'), gmdate('r',$date->getTimestamp()));
        $this->assertSame($date->toString('U'), gmdate('U',$date->getTimestamp()));

        Zend_Date::setOptions(array('format_type' => 'php'));

        // PHP date() format specifier tests
        $date1 = new Zend_Date('2006-01-02 23:58:59', Zend_Date::ISO_8601, 'en_US');
        $date2 = new Zend_Date('2006-01-02 23:58:59', 'Y-m-d H:i:s', 'en_US');
        $this->assertSame($date1->getTimestamp(), $date2->getTimestamp());

        date_default_timezone_set('GMT');
        $date = new Zend_Date(0); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame($date->toString('w'), gmdate('w',$date->getTimestamp()));
        $this->assertSame($date->toString('d'), gmdate('d',$date->getTimestamp()));
        $this->assertSame($date->toString('D', 'en'), gmdate('D',$date->getTimestamp()));
        $this->assertSame($date->toString('j'), gmdate('j',$date->getTimestamp()));
        $this->assertSame($date->toString('l', 'en'), gmdate('l',$date->getTimestamp()));
        $this->assertSame($date->toString('N'), gmdate('N',$date->getTimestamp()));
        $this->assertSame($date->toString('S'), gmdate('S',$date->getTimestamp()));
        $this->assertSame($date->toString('z'), gmdate('z',$date->getTimestamp()));
        $this->assertSame($date->toString('W'), gmdate('W',$date->getTimestamp()));
        $this->assertSame($date->toString('F', 'en'), gmdate('F',$date->getTimestamp()));
        $this->assertSame($date->toString('m'), gmdate('m',$date->getTimestamp()));
        $this->assertSame($date->toString('M', 'en'), gmdate('M',$date->getTimestamp()));
        $this->assertSame($date->toString('n'), gmdate('n',$date->getTimestamp()));
        $this->assertSame($date->toString('t'), gmdate('t',$date->getTimestamp()));
        $this->assertSame($date->toString('L'), gmdate('L',$date->getTimestamp()));
        $this->assertSame($date->toString('o'), gmdate('o',$date->getTimestamp()));
        $this->assertSame($date->toString('Y'), gmdate('Y',$date->getTimestamp()));
        $this->assertSame($date->toString('y'), gmdate('y',$date->getTimestamp()));
        $this->assertSame(strtolower($date->toString('a', 'en')), gmdate('a',$date->getTimestamp()));
        $this->assertSame(strtoupper($date->toString('A', 'en')), gmdate('A',$date->getTimestamp()));
        $this->assertSame($date->toString('B'), gmdate('B',$date->getTimestamp()));
        $this->assertSame($date->toString('g'), gmdate('g',$date->getTimestamp()));
        $this->assertSame($date->toString('G'), gmdate('G',$date->getTimestamp()));
        $this->assertSame($date->toString('h'), gmdate('h',$date->getTimestamp()));
        $this->assertSame($date->toString('H'), gmdate('H',$date->getTimestamp()));
        $this->assertSame($date->toString('i'), gmdate('i',$date->getTimestamp()));
        $this->assertSame($date->toString('s'), gmdate('s',$date->getTimestamp()));
        $this->assertSame($date->toString('e'), date('e',$date->getTimestamp()));
        $this->assertSame($date->toString('I'), gmdate('I',$date->getTimestamp()));
        $this->assertSame($date->toString('O'), gmdate('O',$date->getTimestamp()));
        $this->assertSame($date->toString('P'), gmdate('P',$date->getTimestamp()));
        $this->assertSame($date->toString('T'), gmdate('T',$date->getTimestamp()));
        $this->assertSame($date->toString('Z'), gmdate('Z',$date->getTimestamp()));
        $this->assertSame($date->toString('c'), gmdate('c',$date->getTimestamp()));
        $this->assertSame($date->toString('r'), gmdate('r',$date->getTimestamp()));
        $this->assertSame($date->toString('U'), gmdate('U',$date->getTimestamp()));

        date_default_timezone_set('GMT');
        $date = new Zend_Date(mktime(20,10,0,10,10,2000)); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame($date->toString('w'), gmdate('w',$date->getTimestamp()));
        $this->assertSame($date->toString('d'), gmdate('d',$date->getTimestamp()));
        $this->assertSame($date->toString('D', 'en'), gmdate('D',$date->getTimestamp()));
        $this->assertSame($date->toString('j'), gmdate('j',$date->getTimestamp()));
        $this->assertSame($date->toString('l', 'en'), gmdate('l',$date->getTimestamp()));
        $this->assertSame($date->toString('N'), gmdate('N',$date->getTimestamp()));
        $this->assertSame($date->toString('S'), gmdate('S',$date->getTimestamp()));
        $this->assertSame($date->toString('z'), gmdate('z',$date->getTimestamp()));
        $this->assertSame($date->toString('W'), gmdate('W',$date->getTimestamp()));
        $this->assertSame($date->toString('F', 'en'), gmdate('F',$date->getTimestamp()));
        $this->assertSame($date->toString('m'), gmdate('m',$date->getTimestamp()));
        $this->assertSame($date->toString('M', 'en'), gmdate('M',$date->getTimestamp()));
        $this->assertSame($date->toString('n'), gmdate('n',$date->getTimestamp()));
        $this->assertSame($date->toString('t'), gmdate('t',$date->getTimestamp()));
        $this->assertSame($date->toString('L'), gmdate('L',$date->getTimestamp()));
        $this->assertSame($date->toString('o'), gmdate('o',$date->getTimestamp()));
        $this->assertSame($date->toString('Y'), gmdate('Y',$date->getTimestamp()));
        $this->assertSame($date->toString('y'), gmdate('y',$date->getTimestamp()));
        $this->assertSame(strtolower($date->toString('a', 'en')), gmdate('a',$date->getTimestamp()));
        $this->assertSame(strtoupper($date->toString('A', 'en')), gmdate('A',$date->getTimestamp()));
        $this->assertSame($date->toString('B'), gmdate('B',$date->getTimestamp()));
        $this->assertSame($date->toString('g'), gmdate('g',$date->getTimestamp()));
        $this->assertSame($date->toString('G'), gmdate('G',$date->getTimestamp()));
        $this->assertSame($date->toString('h'), gmdate('h',$date->getTimestamp()));
        $this->assertSame($date->toString('H'), gmdate('H',$date->getTimestamp()));
        $this->assertSame($date->toString('i'), gmdate('i',$date->getTimestamp()));
        $this->assertSame($date->toString('s'), gmdate('s',$date->getTimestamp()));
        $this->assertSame($date->toString('e'), date('e',$date->getTimestamp()));
        $this->assertSame($date->toString('I'), gmdate('I',$date->getTimestamp()));
        $this->assertSame($date->toString('O'), gmdate('O',$date->getTimestamp()));
        $this->assertSame($date->toString('P'), gmdate('P',$date->getTimestamp()));
        $this->assertSame($date->toString('T'), gmdate('T',$date->getTimestamp()));
        $this->assertSame($date->toString('Z'), gmdate('Z',$date->getTimestamp()));
        $this->assertSame($date->toString('c'), gmdate('c',$date->getTimestamp()));
        $this->assertSame($date->toString('r'), gmdate('r',$date->getTimestamp()));
        $this->assertSame($date->toString('U'), gmdate('U',$date->getTimestamp()));

        date_default_timezone_set('Indian/Maldives');
        $date = new Zend_Date(0); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame($date->toString('w'), date('w',$date->getTimestamp()));
        $this->assertSame($date->toString('d'), date('d',$date->getTimestamp()));
        $this->assertSame($date->toString('D', 'en'), date('D',$date->getTimestamp()));
        $this->assertSame($date->toString('j'), date('j',$date->getTimestamp()));
        $this->assertSame($date->toString('l', 'en'), date('l',$date->getTimestamp()));
        $this->assertSame($date->toString('N'), date('N',$date->getTimestamp()));
        $this->assertSame($date->toString('S'), date('S',$date->getTimestamp()));
        $this->assertSame($date->toString('z'), date('z',$date->getTimestamp()));
        $this->assertSame($date->toString('W'), date('W',$date->getTimestamp()));
        $this->assertSame($date->toString('F', 'en'), date('F',$date->getTimestamp()));
        $this->assertSame($date->toString('m'), date('m',$date->getTimestamp()));
        $this->assertSame($date->toString('M', 'en'), date('M',$date->getTimestamp()));
        $this->assertSame($date->toString('n'), date('n',$date->getTimestamp()));
        $this->assertSame($date->toString('t'), date('t',$date->getTimestamp()));
        $this->assertSame($date->toString('L'), date('L',$date->getTimestamp()));
        $this->assertSame($date->toString('o'), date('o',$date->getTimestamp()));
        $this->assertSame($date->toString('Y'), date('Y',$date->getTimestamp()));
        $this->assertSame($date->toString('y'), date('y',$date->getTimestamp()));
        $this->assertSame(strtolower($date->toString('a', 'en')), date('a',$date->getTimestamp()));
        $this->assertSame(strtoupper($date->toString('A', 'en')), date('A',$date->getTimestamp()));
        $this->assertSame($date->toString('B'), date('B',$date->getTimestamp()));
        $this->assertSame($date->toString('g'), date('g',$date->getTimestamp()));
        $this->assertSame($date->toString('G'), date('G',$date->getTimestamp()));
        $this->assertSame($date->toString('h'), date('h',$date->getTimestamp()));
        $this->assertSame($date->toString('H'), date('H',$date->getTimestamp()));
        $this->assertSame($date->toString('i'), date('i',$date->getTimestamp()));
        $this->assertSame($date->toString('s'), date('s',$date->getTimestamp()));
        $this->assertSame($date->toString('e'), date('e',$date->getTimestamp()));
        $this->assertSame($date->toString('I'), date('I',$date->getTimestamp()));
        $this->assertSame($date->toString('O'), date('O',$date->getTimestamp()));
        $this->assertSame($date->toString('P'), date('P',$date->getTimestamp()));
        $this->assertSame($date->toString('T'), date('T',$date->getTimestamp()));
        $this->assertSame($date->toString('Z'), date('Z',$date->getTimestamp()));
        $this->assertSame($date->toString('c'), date('c',$date->getTimestamp()));
        $this->assertSame($date->toString('r'), date('r',$date->getTimestamp()));
        $this->assertSame($date->toString('U'), date('U',$date->getTimestamp()));

        date_default_timezone_set('Indian/Maldives');
        $date = new Zend_Date(mktime(20,10,0,10,10,2000)); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame($date->toString('w'), date('w',$date->getTimestamp()));
        $this->assertSame($date->toString('d'), date('d',$date->getTimestamp()));
        $this->assertSame($date->toString('D', 'en'), date('D',$date->getTimestamp()));
        $this->assertSame($date->toString('j'), date('j',$date->getTimestamp()));
        $this->assertSame($date->toString('l', 'en'), date('l',$date->getTimestamp()));
        $this->assertSame($date->toString('N'), date('N',$date->getTimestamp()));
        $this->assertSame($date->toString('S'), date('S',$date->getTimestamp()));
        $this->assertSame($date->toString('z'), date('z',$date->getTimestamp()));
        $this->assertSame($date->toString('W'), date('W',$date->getTimestamp()));
        $this->assertSame($date->toString('F', 'en'), date('F',$date->getTimestamp()));
        $this->assertSame($date->toString('m'), date('m',$date->getTimestamp()));
        $this->assertSame($date->toString('M', 'en'), date('M',$date->getTimestamp()));
        $this->assertSame($date->toString('n'), date('n',$date->getTimestamp()));
        $this->assertSame($date->toString('t'), date('t',$date->getTimestamp()));
        $this->assertSame($date->toString('L'), date('L',$date->getTimestamp()));
        $this->assertSame($date->toString('o'), date('o',$date->getTimestamp()));
        $this->assertSame($date->toString('Y'), date('Y',$date->getTimestamp()));
        $this->assertSame($date->toString('y'), date('y',$date->getTimestamp()));
        $this->assertSame(strtolower($date->toString('a', 'en')), date('a',$date->getTimestamp()));
        $this->assertSame(strtoupper($date->toString('A', 'en')), date('A',$date->getTimestamp()));
        $this->assertSame($date->toString('B'), date('B',$date->getTimestamp()));
        $this->assertSame($date->toString('g'), date('g',$date->getTimestamp()));
        $this->assertSame($date->toString('G'), date('G',$date->getTimestamp()));
        $this->assertSame($date->toString('h'), date('h',$date->getTimestamp()));
        $this->assertSame($date->toString('H'), date('H',$date->getTimestamp()));
        $this->assertSame($date->toString('i'), date('i',$date->getTimestamp()));
        $this->assertSame($date->toString('s'), date('s',$date->getTimestamp()));
        $this->assertSame($date->toString('e'), date('e',$date->getTimestamp()));
        $this->assertSame($date->toString('I'), date('I',$date->getTimestamp()));
        $this->assertSame($date->toString('O'), date('O',$date->getTimestamp()));
        $this->assertSame($date->toString('P'), date('P',$date->getTimestamp()));
        $this->assertSame($date->toString('T'), date('T',$date->getTimestamp()));
        $this->assertSame($date->toString('Z'), date('Z',$date->getTimestamp()));
        $this->assertSame($date->toString('c'), date('c',$date->getTimestamp()));
        $this->assertSame($date->toString('r'), date('r',$date->getTimestamp()));
        $this->assertSame($date->toString('U'), date('U',$date->getTimestamp()));
        Zend_Date::setOptions(array('format_type' => 'iso'));
    }

    public function testDaylightsaving()
    {
        $date = new Zend_Date('2007.03.25', Zend_Date::DATES);
        $date->set('16:00:00', Zend_Date::TIMES);
        $this->assertEquals($date->get(Zend_Date::W3C), '2007-03-25T16:00:00+05:00');
        $date->set('01:00:00', Zend_Date::TIMES);
        $this->assertEquals($date->get(Zend_Date::W3C), '2007-03-25T01:00:00+05:00');
    }

    public function testSetOptions()
    {
        $options = Zend_Date::setOptions();
        $this->assertTrue(is_array($options));
        $this->assertEquals($options['format_type'], 'iso');

        Zend_Date::setOptions(array('format_type' => 'php'));
        $options = Zend_Date::setOptions();
        $this->assertEquals($options['format_type'], 'php');

        try {
            Zend_Date::setOptions(array('format_type' => 'non'));
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            Zend_Date::setOptions(array('unknown' => 'non'));
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    public function testIsDate()
    {
        $this->assertTrue(Zend_Date::isDate('25.03.2007', 'de_AT'));
        $this->assertTrue(Zend_Date::isDate('2007.03.25', 'YYYY.MM.dd'));
        $this->assertTrue(Zend_Date::isDate('25.Mai.2007', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertTrue(Zend_Date::isDate('25.Mai.2007 10:00:00', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertFalse(Zend_Date::isDate('32.Mai.2007 10:00:00', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertFalse(Zend_Date::isDate('30.Februar.2007 10:00:00', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertFalse(Zend_Date::isDate('30.Februar.2007 30:00:00', 'dd.MMMM.YYYY HH:mm:ss', 'de_AT'));
    }

    public function testToArray()
    {
        $date = new Zend_Date('2006-01-02 23:58:59', Zend_Date::ISO_8601, 'en_US');
        $return = $date->toArray();
        $orig = array('day' => 02, 'month' => 01, 'year' => 2006, 'hour' => 23, 'minute' => 58,
                      'second' => 59, 'timezone' => 'MVT', 'timestamp' => 1136228339, 'weekday' => 1,
                      'dayofyear' => 1, 'week' => '01', 'gmtsecs' => 18000);
        $this->assertEquals($return, $orig);
    }

    public function testFromArray()
    {
        $date = new Zend_Date(array('day' => 04, 'month' => 12, 'year' => 2006, 'hour' => 10,
                                    'minute' => 56, 'second' => 30), 'en_US');
        $this->assertSame($date->getIso(), '2006-12-04T10:56:30+05:00');
    }
}

class Zend_Date_TestHelper extends Zend_Date
{
    public function _setTime($timestamp)
    {
        $this->_getTime($timestamp);
    }

    protected function _getTime($timestamp = null)
    {
        static $_timestamp = null;
        if ($timestamp !== null) {
            $_timestamp = $timestamp;
        }
        if ($_timestamp !== null) {
            return $_timestamp;
        }
        return time();
    }

    public function mktime($hour, $minute, $second, $month, $day, $year, $dst= -1, $gmt = false)
    {
        return parent::mktime($hour, $minute, $second, $month, $day, $year, $dst, $gmt);
    }
}
