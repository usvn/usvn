<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Csv
 */
require_once 'Zend/Translate/Adapter/Csv.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_CsvTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');

        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Csv);

        try {
            $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/nofile.csv', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');

        $this->assertEquals($adapter->toString(), 'Csv');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');

        $this->assertEquals($adapter->translate('Message 1'), 'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5 (en)');

        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en2.csv', 'en', array('separator' => ','));

        $this->assertEquals($adapter->translate('Message 1'), 'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 4,'), 'Message 4 (en)');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5, (en)');
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en_US');

        $this->assertEquals($adapter->isTranslated('Message 1'), true);
        $this->assertEquals($adapter->isTranslated('Message 6'), false);
        $this->assertEquals($adapter->isTranslated('Message 1', true), true);
        $this->assertEquals($adapter->isTranslated('Message 1', true, 'en'), false);
        $this->assertEquals($adapter->isTranslated('Message 1', false, 'es'), false);
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');

        $this->assertEquals($adapter->translate('Message 1'),       'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'),       'Message 5 (en)');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'Message 2');

        $this->assertEquals($adapter->translate('Message 1', 'xx'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 1', 'en_US'), 'Message 1 (en)');

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.csv', 'xx');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions(), array('separator' => ';', 'testoption' => 'testkey', 'clear' => false));
        $this->assertEquals($adapter->getOptions('testoption'), 'testkey');
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');

        $this->assertEquals($adapter->getLocale(), 'en');
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals($adapter->getLocale(), 'en');
        try {
            $adapter->setLocale('nolocale');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
        try {
            $adapter->setLocale('de');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');


        $this->assertEquals($adapter->getList(), array('en' => 'en'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.csv', 'de');
        $this->assertEquals($adapter->getList(), array('en' => 'en', 'de' => 'de'));

        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }
}
