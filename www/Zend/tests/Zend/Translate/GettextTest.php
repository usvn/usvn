<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Gettext
 */
require_once 'Zend/Translate/Adapter/Gettext.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_Translate_GettextTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo');

        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Gettext);

        try {
            $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/nofile.mo', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }

        try {
            $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/test_fileerror.mo', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo');

        $this->assertEquals($adapter->toString(), 'Gettext');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo');

        $this->assertEquals($adapter->translate('Message 1'), 'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5');
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo');

        $this->assertEquals($adapter->isTranslated('Message 1'), true);
        $this->assertEquals($adapter->isTranslated('Message 6'), false);
        $this->assertEquals($adapter->isTranslated('Message 1', true), true);
        $this->assertEquals($adapter->isTranslated('Message 1', true, 'en'), false);
        $this->assertEquals($adapter->isTranslated('Message 1', false, 'es'), false);
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo', 'en');

        $this->assertEquals($adapter->translate('Message 1'),       'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'Message 2');

        $adapter->addTranslation(dirname(__FILE__) . '/_files/testmsg_ru(koi8-r).mo', 'ru');
        // Original message is in KOI8-R.. as unit tests are done in UTF8 we have to convert
        // the returned KOI8-R string into UTF-8
        $translation = iconv("KOI8-R", "UTF-8", $adapter->translate('Message 2', 'ru'));
        $this->assertEquals($translation, 'Сообщение 2 (ru)');

        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 5', 'ru_RU'), 'Message 5');

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/testmsg_ru(koi8-r).mo', 'xx');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo', 'en');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions(), array('testoption' => 'testkey', 'clear' => false));
        $this->assertEquals($adapter->getOptions('testoption'), 'testkey');
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo', 'en');

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
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo', 'en');

        $this->assertEquals($adapter->getList(), array('en' => 'en'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/testmsg_en.mo', 'de');
        $this->assertEquals($adapter->getList(), array('en' => 'en', 'de' => 'de'));

        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testBigEndian()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translate_bigendian.mo', 'sr');
        $this->assertEquals($adapter->translate('Informacje'), 'Informacje');
    }
}
