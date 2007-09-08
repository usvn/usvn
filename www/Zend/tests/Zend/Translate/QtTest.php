<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Qt
 */
require_once 'Zend/Translate/Adapter/Qt.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_QtTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts');

        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Qt);

        try {
            $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/nofile.ts', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts');

        $this->assertEquals($adapter->toString(), 'Qt');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');

        $this->assertEquals($adapter->translate('Message 1'), 'Nachricht 1');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5');
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de_AT');

        $this->assertEquals($adapter->isTranslated('Message 1'), true);
        $this->assertEquals($adapter->isTranslated('Message 6'), false);
        $this->assertEquals($adapter->isTranslated('Message 1', true), true);
        $this->assertEquals($adapter->isTranslated('Message 1', true, 'en'), false);
        $this->assertEquals($adapter->isTranslated('Message 1', false, 'es'), false);
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');

        $this->assertEquals($adapter->translate('Message 1'),       'Nachricht 1');
        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'Message 2');

        $this->assertEquals($adapter->translate('Message 1', 'xx'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 1', 'de'), 'Nachricht 1');

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_de.ts', 'xx');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions(), array('testoption' => 'testkey', 'clear' => false));
        $this->assertEquals($adapter->getOptions('testoption'), 'testkey');
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');

        $this->assertEquals($adapter->getLocale(), 'de');
        $locale = new Zend_Locale('de');
        $adapter->setLocale($locale);
        $this->assertEquals($adapter->getLocale(), 'de');
        try {
            $adapter->setLocale('nolocale');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
        try {
            $adapter->setLocale('en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');

        $this->assertEquals($adapter->getList(), array('de' => 'de'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_de.ts', 'en');
        $this->assertEquals($adapter->getList(), array('en' => 'en', 'de' => 'de'));

        $this->assertTrue($adapter->isAvailable('en'));
        $locale = new Zend_Locale('de');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }
}
