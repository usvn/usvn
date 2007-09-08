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
 * @version    $Id: LocaleTest.php 4521 2007-04-17 09:41:35Z thomas $
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite
// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

/**
 * Zend_Locale
 */
// @todo require_once 'Zend.php';
require_once 'Zend/Locale.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";

/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */
class Zend_LocaleTest extends PHPUnit_Framework_TestCase
{


    /**
     * test for object creation
     * expected object instance
     */
    public function testObjectCreation()
    {
        $this->assertTrue(new Zend_Locale() instanceof Zend_Locale,'Zend_Locale Object not returned');
        $this->assertTrue(new Zend_Locale('root') instanceof Zend_Locale,'Zend_Locale Object not returned');
        $this->assertTrue(new Zend_Locale(Zend_Locale::ENVIRONMENT) instanceof Zend_Locale,'Zend_Locale Object not returned');
        $this->assertTrue(new Zend_Locale(Zend_Locale::BROWSER) instanceof Zend_Locale,'Zend_Locale Object not returned');

        $locale = new Zend_Locale('de');
        $this->assertTrue(new Zend_Locale($locale) instanceof Zend_Locale,'Zend_Locale Object not returned');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testSerialize()
    {
        $value = new Zend_Locale('de_DE');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Locale not serialized');

        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Locale not unserialized');
    }


    /**
     * test toString
     * expected string
     */
    public function testToString()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals($value->toString(), 'de_DE', 'Locale de_DE expected');
        $this->assertEquals($value->__toString(), 'de_DE', 'Value de_DE expected');
    }


    /**
     * test getDefault
     * expected true
     */
    public function testgetDefault()
    {
        $value = new Zend_Locale();
        $default = $value->getDefault();
        $this->assertTrue(is_array($default), 'No Default Locale found');

        $default = $value->getDefault(Zend_Locale::BROWSER);
        $this->assertTrue(is_array($default), 'No Default Locale found');

        $default = $value->getDefault(Zend_Locale::ENVIRONMENT);
        $this->assertTrue(is_array($default), 'No Default Locale found');

        $default = $value->getDefault(Zend_Locale::FRAMEWORK);
        $this->assertTrue(is_array($default), 'No Default Locale found');
    }


    /**
     * test getEnvironment
     * expected true
     */
    public function testLocaleDetail()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals($value->getLanguage(), 'de');
        $this->assertEquals($value->getRegion(), 'AT');

        $value = new Zend_Locale('en_US');
        $this->assertEquals($value->getLanguage(), 'en');
        $this->assertEquals($value->getRegion(), 'US');

        $value = new Zend_Locale('en');
        $this->assertEquals($value->getLanguage(), 'en');
        $this->assertFalse($value->getRegion());
    }


    /**
     * test getEnvironment
     * expected true
     */
    public function testEnvironment()
    {
        $value = new Zend_Locale();
        $default = $value->getEnvironment();
        $this->assertTrue(is_array($default), 'No Environment Locale found');
    }


    /**
     * test getBrowser
     * expected true
     */
    public function testBrowser()
    {
        $value = new Zend_Locale();
        $default = $value->getBrowser();
        $this->assertTrue(is_array($default), 'No Environment Locale found');
    }


    /**
     * test clone
     * expected true
     */
    public function testCloneing()
    {
        $value = new Zend_Locale('de_DE');
        $newvalue = clone $value;
        $this->assertEquals($value->toString(), $newvalue->toString(), 'Cloning Locale failed');
    }


    /**
     * test setLocale
     * expected true
     */
    public function testsetLocale()
    {
        $value = new Zend_Locale('de_DE');
        $value->setLocale('en_US');
        $this->assertEquals($value->toString(), 'en_US', 'Environment Locale not set');

        $value->setLocale('en_AA');
        $this->assertEquals($value->toString(), 'en', 'Environment Locale not set');

        $value->setLocale('xx_AA');
        $this->assertEquals($value->toString(), 'root', 'Environment Locale not set');
    }


    /**
     * test getLanguageTranslationList
     * expected true
     */
    public function testgetLanguageTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getLanguageTranslationList();
        $this->assertTrue(is_array($list), 'Language List not returned');
        $list = $value->getLanguageTranslationList('de');
        $this->assertTrue(is_array($list), 'Language List not returned');
    }


    /**
     * test getLanguageTranslation
     * expected true
     */
    public function testgetLanguageTranslation()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals($value->getLanguageTranslation('de'), 'Deutsch', 'Language Display not returned');
        $this->assertEquals($value->getLanguageTranslation('de', 'en'), 'German', 'Language Display not returned');
        $this->assertFalse($value->getLanguageTranslation('xyz'), 'Language Display should be false');
    }


    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getScriptTranslationList();
        $this->assertTrue(is_array($list), 'Script List not returned');
        
        $list = $value->getScriptTranslationList('de');
        $this->assertTrue(is_array($list), 'Script List not returned');
    }


    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslation()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals($value->getScriptTranslation('Arab'), 'Arabisch', 'Script Display not returned');
        $this->assertEquals($value->getScriptTranslation('Arab', 'en'), 'Arabic', 'Script Display not returned');
        $this->assertFalse($value->getScriptTranslation('xyz'), 'Script Display should be false');
    }


    /**
     * test getCountryTranslationList
     * expected true
     */
    public function testgetCountryTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getCountryTranslationList();
        $this->assertTrue(is_array($list), 'Region List not returned');

        $list = $value->getCountryTranslationList('de');
        $this->assertTrue(is_array($list), 'Region List not returned');
    }


    /**
     * test getCountryTranslation
     * expected true
     */
    public function testgetCountryTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals($value->getCountryTranslation('DE'), 'Deutschland', 'No country found');
        $this->assertEquals($value->getCountryTranslation('DE', 'en'), 'Germany', 'No country found');
        $this->assertFalse($value->getCountryTranslation('xyz'), 'Country Display should be false');
    }


    /**
     * test getTerritoryTranslationList
     * expected true
     */
    public function testgetTerritoryTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getTerritoryTranslationList();
        $this->assertTrue(is_array($list), 'Territory List not returned');

        $list = $value->getTerritoryTranslationList('de');
        $this->assertTrue(is_array($list), 'Territory List not returned');
    }


    /**
     * test getTerritoryTranslation
     * expected true
     */
    public function testgetTerritoryTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals($value->getTerritoryTranslation('002'), 'Afrika', 'No territory found');
        $this->assertEquals($value->getTerritoryTranslation('002', 'en'), 'Africa', 'No territory found');
        $this->assertFalse($value->getTerritoryTranslation('xyz'), 'Territory Display should be false');
    }


    /**
     * test getTranslation
     * expected true
     */
    public function testgetTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertTrue(is_array($value->getTranslation('xx')));
        $this->assertTrue(in_array('language', $value->getTranslation('xx')));

        $this->assertEquals($value->getTranslation('de', 'language'), 'Deutsch');
        $this->assertEquals($value->getTranslation('de', 'language', 'en'), 'German');
        $this->assertFalse($value->getTranslation('xx', 'language'));

        $this->assertEquals($value->getTranslation('Latn', 'script'), 'Lateinisch');
        $this->assertEquals($value->getTranslation('Latn', 'script', 'en'), 'Latin');
        $this->assertFalse($value->getTranslation('xyxy', 'script'));

        $this->assertEquals($value->getTranslation('AT', 'country'), 'Österreich');
        $this->assertEquals($value->getTranslation('AT', 'country', 'en'), 'Austria');
        $this->assertFalse($value->getTranslation('xx', 'country'));

        $this->assertEquals($value->getTranslation('002', 'territory'), 'Afrika');
        $this->assertEquals($value->getTranslation('002', 'territory', 'en'), 'Africa');
        $this->assertFalse($value->getTranslation('xxx', 'territory'));

        $this->assertEquals($value->getTranslation('chinese', 'calendar'), 'Chinesischer Kalender');
        $this->assertEquals($value->getTranslation('chinese', 'calendar', 'en'), 'Chinese Calendar');
        $this->assertFalse($value->getTranslation('xxxxxxx', 'calendar'));

        $this->assertEquals($value->getTranslation('1', 'month'), 'Januar');
        $this->assertEquals($value->getTranslation('1', 'month', 'en'), 'January');
        $this->assertFalse($value->getTranslation('x', 'month'));

        $this->assertEquals($value->getTranslation('1', 'month_short'), 'Jan');
        $this->assertEquals($value->getTranslation('1', 'month_short', 'en'), 'Jan');
        $this->assertFalse($value->getTranslation('x', 'month_short'));

        $this->assertEquals($value->getTranslation('1', 'month_narrow'), 'J');
        $this->assertEquals($value->getTranslation('1', 'month_narrow', 'en'), 'J');
        $this->assertFalse($value->getTranslation('x', 'month_narrow'));

        $this->assertEquals($value->getTranslation('sun', 'day'), 'Sonntag');
        $this->assertEquals($value->getTranslation('sun', 'day', 'en'), 'Sunday');
        $this->assertFalse($value->getTranslation('xxx', 'day'));

        $this->assertEquals($value->getTranslation('sun', 'day_short'), 'So');
        $this->assertEquals($value->getTranslation('sun', 'day_short', 'en'), 'Sun');
        $this->assertFalse($value->getTranslation('xxx', 'day_short'));

        $this->assertEquals($value->getTranslation('sun', 'day_narrow'), 'S');
        $this->assertEquals($value->getTranslation('sun', 'day_narrow', 'en'), 'S');
        $this->assertFalse($value->getTranslation('xxx', 'day_narrow'));

        $this->assertEquals($value->getTranslation('full', 'dateformat'), 'EEEE, d. MMMM yyyy');
        $this->assertEquals($value->getTranslation('full', 'dateformat', 'en'), 'EEEE, MMMM d, yyyy');
        $this->assertFalse($value->getTranslation('xxxx', 'dateformat'));

        $this->assertEquals($value->getTranslation('full', 'timeformat'), "H:mm' Uhr 'z");
        $this->assertEquals($value->getTranslation('full', 'timeformat', 'en'), 'h:mm:ss a v');
        $this->assertFalse($value->getTranslation('xxxx', 'timeformat'));

        $this->assertEquals($value->getTranslation('Europe/Berlin', 'timezone'), 'Berlin');
        $this->assertEquals($value->getTranslation('Europe/Paris', 'timezone', 'en'), 'Paris');
        $this->assertFalse($value->getTranslation('xxxx', 'timezone'));

        $this->assertEquals($value->getTranslation('EUR', 'currency'), 'Euro');
        $this->assertEquals($value->getTranslation('EUR', 'currency', 'en'), 'Euro');
        $this->assertFalse($value->getTranslation('xxx', 'currency'));

        $this->assertEquals($value->getTranslation('CHF', 'currency_sign'), 'SFr.');
        $this->assertEquals($value->getTranslation('CHF', 'currency_sign', 'en'), 'SwF');
        $this->assertFalse($value->getTranslation('xxx', 'currency_sign'));

        $this->assertTrue(array_key_exists('EUR', $value->getTranslation('AT', 'currency_detail')));
        $this->assertTrue(array_key_exists('EUR', $value->getTranslation('AT', 'currency_detail', 'en')));
        $this->assertFalse($value->getTranslation('xxx', 'currency_detail'));

        $this->assertTrue(in_array('014', $value->getTranslation('002', 'territory_detail')));
        $this->assertTrue(in_array('014', $value->getTranslation('002', 'territory_detail', 'en')));
        $this->assertFalse($value->getTranslation('xxx', 'territory_detail'));

        $this->assertTrue(in_array('DE', $value->getTranslation('de', 'language_detail')));
        $this->assertTrue(in_array('DE', $value->getTranslation('de', 'language_detail', 'en')));
        $this->assertFalse($value->getTranslation('xxx', 'language_detail'));
    }


    /**
     * test getTranslationList
     * expected true
     */
    public function testgetTranslationList()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertTrue(is_array($value->getTranslationList()));
        $this->assertTrue(in_array('language', $value->getTranslationList()));

        $this->assertTrue(in_array('Deutsch', $value->getTranslationList('language')));
        $this->assertTrue(in_array('German', $value->getTranslationList('language', 'en')));

        $this->assertTrue(in_array('Lateinisch', $value->getTranslationList('script')));
        $this->assertTrue(in_array('Latin', $value->getTranslationList('script', 'en')));

        $this->assertTrue(in_array('Österreich', $value->getTranslationList('country')));
        $this->assertTrue(in_array('Austria', $value->getTranslationList('country', 'en')));

        $this->assertTrue(in_array('Afrika', $value->getTranslationList('territory')));
        $this->assertTrue(in_array('Africa', $value->getTranslationList('territory', 'en')));

        $this->assertTrue(in_array('Chinesischer Kalender', $value->getTranslationList('calendar')));
        $this->assertTrue(in_array('Chinese Calendar', $value->getTranslationList('calendar', 'en')));

        $this->assertTrue(in_array('Januar', $value->getTranslationList('month')));
        $this->assertTrue(in_array('January', $value->getTranslationList('month', 'en')));

        $this->assertTrue(in_array('Jan', $value->getTranslationList('month_short')));
        $this->assertTrue(in_array('Jan', $value->getTranslationList('month_short', 'en')));

        $this->assertTrue(in_array('J', $value->getTranslationList('month_narrow')));
        $this->assertTrue(in_array('J', $value->getTranslationList('month_narrow', 'en')));

        $this->assertTrue(in_array('Sonntag', $value->getTranslationList('day')));
        $this->assertTrue(in_array('Sunday', $value->getTranslationList('day', 'en')));

        $this->assertTrue(in_array('So', $value->getTranslationList('day_short')));
        $this->assertTrue(in_array('Sun', $value->getTranslationList('day_short', 'en')));

        $this->assertTrue(in_array('S', $value->getTranslationList('day_narrow')));
        $this->assertTrue(in_array('S', $value->getTranslationList('day_narrow', 'en')));

        $this->assertTrue(in_array('EEEE, d. MMMM yyyy', $value->getTranslationList('dateformat')));
        $this->assertTrue(in_array('EEEE, MMMM d, yyyy', $value->getTranslationList('dateformat', 'en')));

        $this->assertTrue(in_array("H:mm' Uhr 'z", $value->getTranslationList('timeformat')));
        $this->assertTrue(in_array("h:mm:ss a z", $value->getTranslationList('timeformat', 'en')));

        $this->assertTrue(in_array('Berlin', $value->getTranslationList('timezone')));
        $this->assertTrue(in_array('Paris', $value->getTranslationList('timezone', 'en')));

        $this->assertTrue(in_array('Euro', $value->getTranslationList('currency')));
        $this->assertTrue(in_array('Euro', $value->getTranslationList('currency', 'en')));

        $this->assertTrue(in_array('SFr.', $value->getTranslationList('currency_sign')));
        $this->assertTrue(in_array('SwF', $value->getTranslationList('currency_sign', 'en')));

        $this->assertTrue(in_array('EUR', $value->getTranslationList('currency_detail')));
        $this->assertTrue(in_array('EUR', $value->getTranslationList('currency_detail', 'en')));

        $this->assertTrue(in_array('AU NF NZ', $value->getTranslationList('territory_detail')));
        $this->assertTrue(in_array('AU NF NZ', $value->getTranslationList('territory_detail', 'en')));

        $this->assertTrue(in_array('CZ', $value->getTranslationList('language_detail')));
        $this->assertTrue(in_array('CZ', $value->getTranslationList('language_detail', 'en')));

        $this->assertTrue(in_array('currency', $value->getTranslationList('xxx')));
        $this->assertTrue(in_array('currency', $value->getTranslationList()));
    }


    /**
     * test for equality
     * expected string
     */
    public function testEquals()
    {
        $value = new Zend_Locale('de_DE');
        $serial = new Zend_Locale('de_DE');
        $serial2 = new Zend_Locale('de_AT');
        $this->assertTrue($value->equals($serial),'Zend_Locale not equals');
        $this->assertFalse($value->equals($serial2),'Zend_Locale equal ?');
    }


    /**
     * test getQuestion
     * expected true
     */
    public function testgetQuestion()
    {
        $value = new Zend_Locale();
        $list = $value->getQuestion();
        $this->assertTrue(isset($list['yes']), 'Question not returned');

        $list = $value->getQuestion('de');
        $this->assertTrue(isset($list['yes']), 'Question not returned');
    }


    /**
     * test getBrowser
     * expected true
     */
    public function testgetBrowser()
    {
        putenv("HTTP_ACCEPT_LANGUAGE=,de,en-UK-US;q=0.5,fr_FR;q=0.2");
        $value = new Zend_Locale();
        $list = $value->getBrowser();
        $this->assertTrue(isset($list['de']), 'language array not returned');
    }


    /**
     * test getHttpCharset
     * expected true
     */
    public function testgetHttpCharset()
    {
        putenv("HTTP_ACCEPT_CHARSET=");
        $value = new Zend_Locale();
        $list = $value->getHttpCharset();
        $this->assertTrue(empty($list), 'language array must be empty');

        putenv("HTTP_ACCEPT_CHARSET=,iso-8859-1, utf-8, utf-16, *;q=0.1");
        $value = new Zend_Locale();
        $list = $value->getHttpCharset();
        $this->assertTrue(isset($list['utf-8']), 'language array not returned');
    }


    /**
     * test isLocale
     * expected boolean
     */
    public function testIsLocale()
    {
        $locale = new Zend_Locale('ar');
        $this->assertEquals(Zend_Locale::isLocale($locale), 'ar', "ar expected");
        $this->assertEquals(Zend_Locale::isLocale('de'), 'de', "de expected");
        $this->assertEquals(Zend_Locale::isLocale('de_AT'), 'de_AT', "de_AT expected");
        $this->assertEquals(Zend_Locale::isLocale('de_xx'), 'de', "de expected");
        $this->assertFalse(Zend_Locale::isLocale('yy'), "false expected");
        $this->assertFalse(Zend_Locale::isLocale(1234), "false expected");
        $locale = Zend_Locale::isLocale('', true);
        $this->assertTrue(is_string($locale), "true expected");
    }
}
