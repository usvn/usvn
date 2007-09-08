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
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DataTest.php 3776 2007-03-06 22:50:56Z thomas $
 */

/**
 * Zend_Locale_Data
 */
require_once 'Zend/Locale/Data.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */
class Zend_Locale_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for reading with standard locale
     * expected array
     */
    public function testLDMLNoLocale()
    {
        $this->assertTrue(is_array(Zend_Locale_Data::getContent(null, 'languagelist')),'array expected');

        try {
            $value = Zend_Locale_Data::getContent('nolocale','languagelist');
            $this->fail('locale should throw exception');
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $locale = new Zend_Locale('de');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent($locale, 'languagelist')),'array expected');
    }


    /**
     * test for reading without type
     * expected empty array
     */
    public function testLDMLNoType()
    {
        try {
            $value = Zend_Locale_Data::getContent('de','');
            $this->fail('content should throw an exception');
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Data::getContent('de','xxxxxxx');
            $this->fail('content should throw an exception');
        } catch (Zend_Locale_Exception $e) {
            // success
        }
    }


    /**
     * test for reading the languagelist from locale
     * expected array
     */
    public function testLDMLReadingLanguageList()
    {
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de','languagelist')),'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'scriptlist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'variantlist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'keylist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'typelist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'type', 'calendar')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'territorylist')), 'array expected');

        $value = Zend_Locale_Data::getContent('de', 'language', 'de');
        $this->assertEquals($value['de'], 'Deutsch', 'wrong content');

        $value = Zend_Locale_Data::getContent('de_AT', 'script', 'Arab');
        $this->assertEquals($value['Arab'], 'Arabisch', 'wrong content');

        $value = Zend_Locale_Data::getContent('de_AT', 'variant', 'POSIX');
        $this->assertEquals($value['POSIX'], 'Posix', 'wrong content');

        $value = Zend_Locale_Data::getContent('de_AT', 'key', 'collation');
        $this->assertEquals($value['collation'], 'Sortierung', 'wrong content');

        $value = Zend_Locale_Data::getContent('de_AT', 'type', 'chinese');
        $this->assertEquals($value['chinese'], 'Chinesischer Kalender', 'wrong value');

        $value = Zend_Locale_Data::getContent('de_AT', 'territory', 'AT');
        $this->assertEquals($value['AT'], 'Österreich', 'wrong content');

    }


    /**
     * test for reading orientation from locale
     * expected array
     */
    public function testLDMLReadingOrientation()
    {
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'orientation')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'casing')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'characters')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'delimiters')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'measurement')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'papersize')), 'array expected');
    }


    /**
     * test for reading datechars from locale
     * expected array
     */
    public function testLDMLReadingDatechars()
    {
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'datechars')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'defcalendarformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'defmonthformat', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'monthlist', array('gregorian', 'format', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'defdayformat', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'daylist', array('gregorian', 'format', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'week', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'weekend', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'daytime', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'erashortlist', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'erashort', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'eralist', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'era', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'defdateformat', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'dateformat', array('gregorian', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'deftimeformat', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timeformat', array('gregorian', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'datetimeformat', array('gregorian', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'calendarfields', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'relativedates', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'relativedate', array('gregorian', 'day'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezones')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezone', 'Europe/Berlin')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonestandard')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonestandardshort')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonedaylight')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonedaylightshort')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezoneformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'numbersymbols')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'decimalnumberformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'scientificnumberformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'percentnumberformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencyformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencynames')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencyname', 'EUR')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencysymbols')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencysymbol', 'EUR')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'questionstrings')), 'array expected');

        $value = Zend_Locale_Data::getContent('de_AT', 'month', array('gregorian', 'format', 'wide', 12));
        $this->assertEquals($value['12'], 'Dezember', 'wrong value');

        $value = Zend_Locale_Data::getContent('de_AT', 'day', array('gregorian', 'format', 'wide', 'mon'));
        $this->assertEquals($value['mon'], 'Montag', 'wrong value');

        $this->assertTrue(is_array(Zend_Locale_Data::getContent('ar', 'month', array('islamic', 'format', 'abbreviated', '1'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('az_AZ', 'language', 'az')), 'array expected');
    }


    /**
     * test for supplemental data
     * expected array
     */
    public function testLDMLSupplementalData()
    {
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyfraction', 'JPY')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencydigitlist', 'JPY')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyroundinglist', 'JPY')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyforregion', '830')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyforregionlist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'regionforterritory', '001')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'regionforterritorylist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'scriptforlanguage', 'az')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'scriptforlanguagelist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'territoryforlanguage', 'az')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'territoryforlanguagelist')), 'array expected');
    }
}