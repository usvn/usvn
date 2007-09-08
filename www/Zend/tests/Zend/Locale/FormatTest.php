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
 * @package    Zend_Format
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FormatTest.php 5368 2007-06-17 19:16:50Z thomas $
 */


/**
 * Zend_Locale_Format
 */
require_once 'Zend/Locale/Format.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */
class Zend_Locale_FormatTest extends PHPUnit_Framework_TestCase
{

    /**
     * teardown / cleanup
     */
    public function tearDown()
    {
        // if the setlocale option is enabled, then don't change the setlocale below
        if (defined('TESTS_ZEND_LOCALE_FORMAT_SETLOCALE') && TESTS_ZEND_LOCALE_FORMAT_SETLOCALE === false) {
            // I'm anticipating possible platform inconsistencies, so I'm leaving some debug comments for now.
            //echo '<<<', setlocale(LC_NUMERIC, '0'); // show locale before changing
            setlocale(LC_ALL, 'C'); // attempt to restore global setting i.e. test teardown
            //echo '>>>', setlocale(LC_NUMERIC, '0'); // show locale after changing
            //echo "\n";
        } else if (defined('TESTS_ZEND_LOCALE_FORMAT_SETLOCALE')) {
            setlocale(LC_ALL, TESTS_ZEND_LOCALE_FORMAT_SETLOCALE);
        }
    }


    /**
     * test getNumber
     * expected integer
     */
    public function testGetNumber()
    {
        $this->assertEquals(Zend_Locale_Format::getNumber(0), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(-1234567), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(1234567), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(0.1234567), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(-1234567.12345), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(1234567.12345), 1234567.12345, "value 1234567.12345 expected");
        $options = array('locale' => 'de');
        $this->assertEquals(Zend_Locale_Format::getNumber('0', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1234567', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1234567', $options), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('0,1234567', $options), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1.234.567,12345', $options), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1.234.567,12345', $options), 1234567.12345, "value 1234567.12345 expected");
        $options = array('locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::getNumber('0', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1234567', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1.234.567', $options), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('0,1234567', $options), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1.234.567,12345', $options), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1.234.567,12345', $options), 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test to number
     * expected string
     */
    public function testToNumber()
    {
        $this->assertEquals(Zend_Locale_Format::toNumber(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0, array('locale' => 'de')), '0', "string 0 expected");
        $options = array('locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::toNumber(0, $options), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567, $options), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567, $options), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0.1234567, $options), '0,1234567', "string 0,1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, $options), '-1.234.567,12345', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, $options), '1.234.567,12345', "value 1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('locale' => 'ar_QA')), '1234567٫12345', "value 1234567٫12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, array('locale' => 'ar_QA')), '1234567٫12345-', "value 1234567٫12345- expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('locale' => 'dz_BT')), '12,34,567.12345', "value 12,34,567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, array('locale' => 'mk_MK')), '-(1.234.567,12345)', "value -(1.234.567,12345) expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(452.25, array('locale' => 'en_US')), '452.25', "value 452.25 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(54321.1234, array('locale' => 'en_US')), '54,321.1234', "value 54,321.1234 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1.234567, array('locale' => 'de_DE', 'precision' => 2, 'number_format' => '0.00')), '1,23', "value 1,23 expected");
    }


    /**
     * test isNumber
     * expected boolean
     */
    public function testIsNumber()
    {
        $this->assertEquals(Zend_Locale_Format::isNumber('-1.234.567,12345', array('locale' => 'de_AT')), true, "true expected");
        $this->assertEquals(Zend_Locale_Format::isNumber('textwithoutnumber', array('locale' => 'de_AT')), false, "false expected");
    }


    /**
     * test getFloat
     * expected exception
     */
    public function testgetFloat()
    {
        try {
            $value = Zend_Locale_Format::getFloat('nocontent');
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(Zend_Locale_Format::getFloat(0), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(-1234567), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(1234567), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(0.1234567), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(-1234567.12345), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(1234567.12345), 1234567.12345, "value 1234567.12345 expected");
        $options = array('locale' => 'de');
        $this->assertEquals(Zend_Locale_Format::getFloat('0', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1234567', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1234567', $options), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0,1234567', $options), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1.234.567,12345', $options), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', $options), 1234567.12345, "value 1234567.12345 expected");
        $options = array('locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::getFloat('0', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1234567', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567', $options), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0,1234567', $options), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1.234.567,12345', $options), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', $options), 1234567.12345, "value 1234567.12345 expected");
        $options = array('precision' => 2, 'locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::getFloat('0', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1234567', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567', $options), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0,1234567', $options), 0.12, "value 0.12 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1.234.567,12345', $options), -1234567.12, "value -1234567.12 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', $options), 1234567.12, "value 1234567.12 expected");
        $options = array('precision' => 7, 'locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', $options), '1234567.12345', "value 1234567.12345 expected");
    }


    /**
     * test toFloat
     * expected string
     */
    public function testToFloat()
    {
        $this->assertEquals(Zend_Locale_Format::toFloat(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0, array('locale' => 'de')), '0', "string 0 expected");
        $options = array('locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::toFloat(0, $options), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567, $options), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567, $options), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0.1234567, $options), '0,1234567', "string 0,1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, $options), '-1.234.567,12345', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, $options), '1.234.567,12345', "value 1.234.567,12345 expected");
        $options = array('locale' => 'ar_QA');
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, $options), '1234567٫12345', "value 1234567٫12345 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, $options), '1234567٫12345-', "value 1234567٫12345- expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, array('locale' => 'dz_BT')), '12,34,567.12345', "value 12,34,567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, array('locale' => 'mk_MK')), '-(1.234.567,12345)', "value -(1.234.567,12345) expected");
        $options = array('precision' => 2, 'locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::toFloat(0, $options), '0,00', "value 0,00 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567, $options), '-1.234.567,00', "value -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567, $options), '1.234.567,00', "value 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0.1234567, $options), '0,12', "value 0,12 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, $options), '-1.234.567,12', "value -1.234.567,12 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, $options), '1.234.567,12', "value 1.234.567,12 expected");
        $options = array('precision' => 7, 'locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, $options), '1.234.567,1234500', "value 1.234.567,12345 expected");
    }


    /**
     * test isFloat
     * expected boolean
     */
    public function testIsFloat()
    {
        $this->assertEquals(Zend_Locale_Format::isFloat('-1.234.567,12345', array('locale' => 'de_AT')), true, "true expected");
        $this->assertEquals(Zend_Locale_Format::isFloat('textwithoutnumber', array('locale' => 'de_AT')), false, "false expected");
    }


    /**
     * test getInteger
     * expected integer
     */
    public function testgetInteger()
    {
        $this->assertEquals(Zend_Locale_Format::getInteger(0), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(-1234567), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(1234567), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(0.1234567), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(-1234567.12345), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(1234567.12345), 1234567, "value 1234567 expected");
        $options = array('locale' => 'de');
        $this->assertEquals(Zend_Locale_Format::getInteger('0', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1234567', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1234567', $options), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('0,1234567', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1.234.567,12345', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1.234.567,12345', $options), 1234567, "value 1234567 expected");
        $options = array('locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::getInteger('0', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1234567', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1.234.567', $options), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('0,1234567', $options), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1.234.567,12345', $options), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1.234.567,12345', $options), 1234567, "value 1234567 expected");
    }


    /**
     * test toInteger
     * expected string
     */
    public function testtoInteger()
    {
        $this->assertEquals(Zend_Locale_Format::toInteger(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(0, array('locale' => 'de')), '0', "string 0 expected");
        $options = array('locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::toInteger(0, $options), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567, $options), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567, $options), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(0.1234567, $options), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567.12345, $options), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567.12345, $options), '1.234.567', "value 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567.12345, array('locale' => 'ar_QA')), '1234567', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567.12345, array('locale' => 'ar_QA')), '1234567-', "value 1234567- expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567.12345, array('locale' => 'dz_BT')), '12,34,567', "value 12,34,567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567.12345, array('locale' => 'mk_MK')), '-(1.234.567)', "value -(1.234.567) expected");
    }


    /**
     * test isInteger
     * expected boolean
     */
    public function testIsInteger()
    {
        $this->assertEquals(Zend_Locale_Format::isInteger('-1.234.567,12345', array('locale' => 'de_AT')), TRUE, "TRUE expected");
        $this->assertEquals(Zend_Locale_Format::isInteger('textwithoutnumber', array('locale' => 'de_AT')), FALSE, "FALSE expected");
    }


    /**
     * test getDate
     * expected array
     */
    public function testgetDate()
    {
        try {
            $value = Zend_Locale_Format::getDate('no content');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        $this->assertEquals(is_array(Zend_Locale_Format::getDate('10.10.06')), true, "array expected");
        $this->assertEquals(count(Zend_Locale_Format::getDate('10.10.06', array('date_format' => 'dd.MM.yy'))), 5, "array with 5 tags expected");

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'dd.MM.yy'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 06, 'Year 06 expected');

        $value = Zend_Locale_Format::getDate('10.11.2006', array('date_format' => 'dd.MM.yy'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('2006.13.01', array('date_format' => 'dd.MM.yy'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.13.01', array('date_format' => 'dd.MM.yy', 'fix_date' => true));
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('2006.01.13', array('date_format' => 'dd.MM.yy'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.01.13', array('date_format' => 'dd.MM.yy', 'fix_date' => true));
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('101106', array('date_format' => 'ddMMyy'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $value = Zend_Locale_Format::getDate('10112006', array('date_format' => 'ddMMyyyy'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 Nov 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 November 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('November 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('November 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('Nov 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('2006 10 Nov', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006 10 Nov', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('2006 Nov 10', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'yy.dd.MM'));
        $this->assertEquals($value['day'], 11, 'Day 11 expected');
        $this->assertEquals($value['month'], 6, 'Month 6 expected');
        $this->assertEquals($value['year'], 10, 'Year 10 expected');

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'dd.yy.MM'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 6, 'Month 6 expected');
        $this->assertEquals($value['year'], 11, 'Year 11 expected');

        $value = Zend_Locale_Format::getDate('10.11.06', array('locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $value = Zend_Locale_Format::getDate('10.11.2006', array('locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('2006.13.01', array('locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.13.01', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('2006.01.13', array('locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.01.13', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('101106', array('locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $value = Zend_Locale_Format::getDate('10112006', array('locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 Nov 2006', array('locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 November 2006', array('locale' => 'de_AT'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('November 10 2006', array('locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('November 10 2006', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('April 10 2006', array('locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('April 10 2006', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 4, 'Month 4 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', array('locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('Nov 10 2006', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', array('locale' => 'de_AT'));
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006 10 Nov', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('01.April.2006', array('date_format' => 'dd.MMMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals($value['day'], 1, 'Day 1 expected');
        $this->assertEquals($value['month'], 4, 'Month 4 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('Montag, 01.April.2006', array('date_format' => 'EEEE, dd.MMMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals($value['day'], 1, 'Day 1 expected');
        $this->assertEquals($value['month'], 4, 'Month 4 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('13.2006.11', array('date_format' => 'dd.MM.yy'));
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        Zend_Locale_Format::setOptions(array('format_type' => 'php'));
        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'd.m.Y'));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'd.m.Y', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $this->assertEquals(is_array(Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'))), true, "array expected");
        Zend_Locale_Format::setOptions(array('format_type' => 'iso'));

        $value = Zend_Locale_Format::getDate('2006 Nov 10', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('anything February 31 2007.', array('date_format' => 'M d Y', 'locale'=>'en'));
        $this->assertEquals($value['day'], 31, 'Day 31 expected');
        $this->assertEquals($value['month'], 2, 'Month 2 expected');
        $this->assertEquals($value['year'], 2007, 'Year 2007 expected');
    }


    /**
     * test getTime
     * expected array
     */
    public function testgetTime()
    {
        try {
            $value = Zend_Locale_Format::getTime('no content');
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(is_array(Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'))), true, "array expected");
        $options = array('date_format' => 'h:mm:ss a', 'locale' => 'en');
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('11:14:55 am', $options)), true, "array expected");
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('12:14:55 am', $options)), true, "array expected");
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('11:14:55 pm', $options)), true, "array expected");
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('12:14:55 pm', $options)), true, "array expected");

        try {
            $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'nocontent'));
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'ZZZZ'));
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss.x'));
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(count(Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'))), 5, "array with 5 tags expected");

        $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'));
        $this->assertEquals($value['hour'], 13, 'Hour 13 expected');
        $this->assertEquals($value['minute'], 14, 'Minute 14 expected');
        $this->assertEquals($value['second'], 55, 'Second 55 expected');

        $value = Zend_Locale_Format::getTime('131455', array('date_format' => 'HH:mm:ss'));
        $this->assertEquals($value['hour'], 13, 'Hour 13 expected');
        $this->assertEquals($value['minute'], 14, 'Minute 14 expected');
        $this->assertEquals($value['second'], 55, 'Second 55 expected');
    }


    /**
     * test isDate
     * expected boolean
     */
    public function testIsDate()
    {
        $this->assertTrue(Zend_Locale_Format::checkDateFormat('13.Nov.2006', array('locale' => 'de_AT')), "true expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('13.XXX.2006', array('locale' => 'ar_EG')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('nodate'), "false expected");

        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.01.2006', array('date_format' => 'M-d-y')), "false expected");
        $this->assertTrue(Zend_Locale_Format::checkDateFormat('20.01.2006', array('date_format' => 'd-M-y')), "true expected");

        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.April', array('date_format' => 'dd.MMMM.YYYY')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.April', array('date_format' => 'MMMM.YYYY')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20', array('date_format' => 'dd.MMMM.YYYY')), "false expected");
        $this->assertTrue(Zend_Locale_Format::checkDateFormat('April.2007', array('date_format' => 'MMMM.YYYY')), "true expected");
        $this->assertTrue(Zend_Locale_Format::checkDateFormat('20.April.2007', array('date_format' => 'dd.YYYY')), "true expected");

        $this->assertFalse(Zend_Locale_Format::checkDateFormat('2006.04', array('date_format' => 'yyyy.MMMM.dd')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.04.2007 10:11', array('date_format' => 'dd.MMMM.yyyy HH:mm:ss')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.04.2007 10:20', array('date_format' => 'dd.MMMM.yyyy HH:ss:mm')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.04.2007 00:20', array('date_format' => 'dd.MMMM.yyyy ss:mm:HH')), "false expected");
    }


    /**
     * test checkDateFormat -> time
     * expected boolean
     */
    public function testCheckTime()
    {
        $this->assertTrue(Zend_Locale_Format::checkDateFormat('13:10:55', array('date_format' => 'HH:mm:ss', 'locale' => 'de_AT')), "true expected");
        $this->assertTrue(Zend_Locale_Format::checkDateFormat('11:10:55 am', array('date_format' => 'HH:mm:ss', 'locale' => 'ar_EG')), "true expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('notime'), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('13:10', array('date_format' => 'HH:mm:ss', 'locale' => 'de_AT')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('13', array('date_format' => 'HH:mm', 'locale' => 'de_AT')), "false expected");
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('00:13', array('date_format' => 'ss:mm:HH', 'locale' => 'de_AT')), "false expected");
    }


    /**
     * test toNumberSystem
     * expected string
     */
    public function testToNumberSystem()
    {
        try {
            $value = Zend_Locale_Format::convertNumerals('١١٠', 'xxxx');
            $this->fail("no conversion expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Format::convertNumerals('١١٠', 'Arab', 'xxxx');
            $this->fail("no conversion expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(Zend_Locale_Format::convertNumerals('١١٠', 'Arab'), '110', "110 expected");
        $this->assertEquals(Zend_Locale_Format::convertNumerals('١١٠', 'Arab', 'Deva'), '११०', "११० expected");
        $this->assertEquals(Zend_Locale_Format::convertNumerals('١١٠', 'arab', 'dEVa'), '११०', "११० expected");
        $this->assertEquals(Zend_Locale_Format::convertNumerals('110', 'Latn', 'Arab'), '١١٠', "١١٠ expected");
        $this->assertEquals(Zend_Locale_Format::convertNumerals('110', 'latn', 'Arab'), '١١٠', "١١٠ expected");
    }

    /**
     * test toNumberFormat
     * expected string
     */
    public function testToNumberFormat()
    {
        $locale = new Zend_Locale('de_AT');
        $this->assertEquals(Zend_Locale_Format::toNumber(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0, array('locale' => 'de')), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0, array('locale' => $locale)), '0', "string 0 expected");
        $options = array('locale' => 'de_AT');
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567, $options), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567, $options), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0.1234567, $options), '0,1234567', "string 0,1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, $options), '-1.234.567,12345', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, $options), '1.234.567,12345', "value 1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '##0', 'locale' => 'de_AT')), '1234567', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '#,#0', 'locale' => 'de_AT')), '1.23.45.67', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '##0.00', 'locale' => 'de_AT')), '1234567,12', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '##0.###', 'locale' => 'de_AT')), '1234567,12345', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '#,#0', 'locale' => 'de_AT')), '1.23.45.67', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '#,##,##0', 'locale' => 'de_AT')), '12.34.567', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '#,##0.00', 'locale' => 'de_AT')), '1.234.567,12', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '#,#0.00', 'locale' => 'de_AT')), '1.23.45.67,12', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, array('number_format' => '##0;##0-', 'locale' => 'de_AT')), '1234567-', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, array('number_format' => '##0;##0-', 'locale' => 'de_AT')), '1234567', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567, array('number_format' => '#,##0.00', 'locale' => 'de_AT')), '1.234.567,00', "value 1234567,00 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.123, array('precision' => 2, 'locale' => 'de_AT')), '1.234.567,12', "value 1234567,12 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.123, array('number_format' => '#0.00-', 'locale' => 'de_AT')), '1234567,12-', "value 1234567,12 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-12345.67, array('precision' => 0, 'locale' => 'de_AT')), '-12.345', "value -12.345 expected");
    }


    /**
     * test toNumberFormat2
     * expected string
     */
    public function testToNumberFormat2()
    {
        $this->assertEquals((double) Zend_Locale_Format::toNumber(1.7, array('locale' => 'en')), (double) 1.7);
        $this->assertEquals((double) Zend_Locale_Format::toNumber(2.3, array('locale' => 'en')), (double) 2.3);
    }

    /**
     * test setOption
     * expected boolean
     */
    public function testSetOption()
    {
        $this->assertEquals(count(Zend_Locale_Format::setOptions(array('format_type' => 'php'))), 6);
        $this->assertTrue(is_array(Zend_Locale_Format::setOptions()));
        try {
            $this->assertTrue(Zend_Locale_Format::setOptions(array('format_type' => 'xxx')));
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        try {
            $this->assertTrue(Zend_Locale_Format::setOptions(array('myformat' => 'xxx')));
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $format = Zend_Locale_Format::setOptions(array('locale' => 'de', 'number_format' => Zend_Locale_Format::STANDARD));
        $test = Zend_Locale_Data::getContent('de', 'decimalnumberformat');
        $this->assertEquals($format['number_format'], $test['default']);

        try {
            $this->assertFalse(Zend_Locale_Format::setOptions(array('number_format' => array('x' => 'x'))));
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $format = Zend_Locale_Format::setOptions(array('locale' => 'de', 'date_format' => Zend_Locale_Format::STANDARD));
        $test = Zend_Locale_Format::getDateFormat('de');
        $this->assertEquals($format['date_format'], $test);

        try {
            $this->assertFalse(Zend_Locale_Format::setOptions(array('date_format' => array('x' => 'x'))));
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $this->assertFalse(is_array(Zend_Locale_Format::setOptions(array('fix_date' => 'no'))));
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $format = Zend_Locale_Format::setOptions(array('locale' => Zend_Locale_Format::STANDARD));
        $locale = new Zend_Locale();
        $this->assertEquals($format['locale'], $locale);

        try {
            $this->assertFalse(is_array(Zend_Locale_Format::setOptions(array('locale' => 'nolocale'))));
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $this->assertFalse(is_array(Zend_Locale_Format::setOptions(array('precision' => 50))));
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        // test interaction between class-wide default date format and using locale's default format
        try {
            $result = array('date_format' => 'MMM d, yyyy', 'locale' => 'en_US', 'month' => '07',
                    'day' => '4', 'year' => '2007');
            Zend_Locale_Format::setOptions(array('format_type' => 'iso', 'date_format' => 'MMM d, yyyy', 'locale' => 'en_US')); // test setUp
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        try {
            // uses global date_format with global locale
            $this->assertSame($result, Zend_Locale_Format::getDate('July 4, 2007'));
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        try {
            // uses global date_format with given locale
            $this->assertSame($result, Zend_Locale_Format::getDate('July 4, 2007', array('locale' => 'en_US')));
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        try {
            // sets a new global date format
            Zend_Locale_Format::setOptions(array('date_format' => 'M-d-y'));
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        try {
            // uses global date format with given locale
            // but this date format differs from the original set... (MMMM d, yyyy != M-d-y)
            $expected = Zend_Locale_Format::getDate('July 4, 2007', array('locale' => 'en_US'));
            $this->assertSame($result['year'] , $expected['year']);
            $this->assertSame($result['month'], $expected['month']);
            $this->assertSame($result['day']  , $expected['day']);
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        try {
            // the following should not be used... instead of null, Zend_Locale_Format::STANDARD should be used
            // uses given local with standard format from this locale
            $this->assertSame($result,
                Zend_Locale_Format::getDate('July 4, 2007', array('locale' => 'en_US', 'date_format' => null)));
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        try {
            // uses given locale with standard format from this locale
            $this->assertSame($result,
                Zend_Locale_Format::getDate('July 4, 2007',
                    array('locale' => 'en_US', 'date_format' => Zend_Locale_Format::STANDARD)));
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        try {
            // uses standard locale with standard format from this locale
            $expect = Zend_Locale_Format::getDate('July 4, 2007', array('locale' => Zend_Locale_Format::STANDARD));
            $testlocale = new Zend_Locale();
            $this->assertEquals($expect['locale'],$testlocale);
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
        Zend_Locale_Format::setOptions(array('date_format' => null, 'locale' => null)); // test tearDown
    }


    /**
     * test convertPhpToIso
     * expected boolean
     */
    public function testConvertPhpToIso()
    {
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('d'), 'dd');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('D'), 'EEE');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('j'), 'd');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('l'), 'EEEE');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('N'), 'e');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('S'), 'SS');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('w'), 'eee');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('z'), 'D');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('W'), 'w');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('F'), 'MMMM');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('m'), 'MM');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('M'), 'MMM');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('n'), 'M');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('t'), 'ddd');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('L'), 'l');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('o'), 'YYYY');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('Y'), 'yyyy');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('y'), 'yy');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('a'), 'a');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('A'), 'a');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('B'), 'B');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('g'), 'h');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('G'), 'H');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('h'), 'hh');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('H'), 'HH');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('i'), 'mm');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('s'), 'ss');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('e'), 'zzzz');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('I'), 'I');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('O'), 'Z');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('P'), 'ZZZZ');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('T'), 'z');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('Z'), 'X');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('c'), 'yyyy-MM-ddTHH:mm:ssZZZZ');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('r'), 'r');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('U'), 'U');
        $this->assertSame(Zend_Locale_Format::convertPhpToIsoFormat('His'), 'HHmmss');
    }


    /**
     * Test toFloat()/toNumber() when a different setlocale() is in effect,
     * where the locale does not use '.' as the decimal place separator.
     * expected string
     */
    public function testToFloatSetlocale()
    {
        setlocale(LC_ALL, 'fr_FR@euro'); // test setup

        //var_dump( setlocale(LC_NUMERIC, '0')); // this is the specific setting of interest
        $locale_fr = new Zend_Locale('fr_FR');
        $locale_en = new Zend_Locale('en_US');
        $params_fr = array('precision' => 2, 'locale' => $locale_fr);
        $params_en = array('precision' => 2, 'locale' => $locale_en);
        $myFloat = 1234.5;
        $test1 = Zend_Locale_Format::toFloat($myFloat, $params_fr);
        $test2 = Zend_Locale_Format::toFloat($myFloat, $params_en);
        $this->assertEquals($test1, "1 234,50");
        $this->assertEquals($test2, "1,234.50");
        // placing tearDown here (i.e. restoring locale) won't work, if test already failed/aborted above.
    }
}
