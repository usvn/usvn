<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Array
 */
require_once 'Zend/Translate/Adapter/Array.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_Translate_ArrayTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ));

        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ));

        $this->assertEquals($adapter->toString(), 'Array');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ));

        $this->assertEquals($adapter->translate('msg1'), 'Message 1 (en)');
        $this->assertEquals($adapter->translate('msg4'), 'msg4');
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ));

        $this->assertEquals($adapter->isTranslated('msg1'), true);
        $this->assertEquals($adapter->isTranslated('msg4'), false);
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ),
                                                    'en');

        $this->assertEquals($adapter->translate('msg1'), 'Message 1 (en)');
        $this->assertEquals($adapter->translate('msg4'), 'msg4');

        $adapter->addTranslation(array('msg4' => 'Message 4 (en)',
                                       'msg5' => 'Message 5 (en)',
                                       'msg6' => 'Message 6 (en)'
                                      ),'en');
        $this->assertEquals($adapter->translate('msg5'), 'Message 5 (en)');

        $adapter->addTranslation(array('msg1' => 'Message 1 (ru)',
                                       'msg2' => 'Message 2 (ru)',
                                       'msg3' => 'Message 3 (ru)'
                                      ), 'ru');
        $this->assertEquals($adapter->translate('msg1', 'ru'), 'Message 1 (ru)');

        $adapter->addTranslation(array('msg4' => 'Message 4 (ru)',
                                       'msg5' => 'Message 5 (ru)',
                                       'msg6' => 'Message 6 (ru)'
                                      ), 'ru',
                                 array('clear' => true));
        $this->assertEquals($adapter->translate('msg2', 'ru'), 'msg2');
        $this->assertEquals($adapter->translate('msg4', 'ru'), 'Message 4 (ru)');

        $this->assertEquals($adapter->translate('msg1', 'xx'), 'msg1');
        $this->assertEquals($adapter->translate('msg4', 'ru_RU'), 'Message 4 (ru)');

        try {
            $adapter->addTranslation(array('msg1' => 'Message 1 (ru)',
                                           'msg2' => 'Message 2 (ru)',
                                           'msg3' => 'Message 3 (ru)'
                                          ), 'xx');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ), 'en');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions(), array('testoption' => 'testkey', 'clear' => false));
        $this->assertEquals($adapter->getOptions('testoption'), 'testkey');
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ), 'en');

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
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)',
                                                          'msg2' => 'Message 2 (en)',
                                                          'msg3' => 'Message 3 (en)',
                                                         ), 'en');


        $this->assertEquals($adapter->getList(), array('en' => 'en'));
        $adapter->addTranslation(array('msg1'), 'de');
        $this->assertEquals($adapter->getList(), array('en' => 'en', 'de' => 'de'));

        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }
}
