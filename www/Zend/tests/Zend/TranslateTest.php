<?php

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 */


/**
 * Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_TranslateTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array());
        $this->assertTrue($lang instanceof Zend_Translate);
    }

    public function testLocaleInitialization()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array(), 'en');
        $this->assertEquals($lang->getLocale(), 'en');
    }

    public function testDefaultLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array());

        $defaultLocale = new Zend_Locale();
        $this->assertEquals($lang->getLocale(), $defaultLocale->toString());
    }

    public function testGetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY , array(), 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);

        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/_files/testmsg_en.mo', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Gettext);

        $lang = new Zend_Translate(Zend_Translate::AN_TMX , dirname(__FILE__) . '/Translate/_files/translation_en.tmx', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Tmx);

        $lang = new Zend_Translate(Zend_Translate::AN_CSV , dirname(__FILE__) . '/Translate/_files/translation_en.csv', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Csv);

        $lang = new Zend_Translate(Zend_Translate::AN_XLIFF , dirname(__FILE__) . '/Translate/_files/translation_en.xliff', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Xliff);

        $lang = new Zend_Translate('qt' , dirname(__FILE__) . '/Translate/_files/translation_de.ts', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Qt);

        try {
            $lang = new Zend_Translate('sql' , dirname(__FILE__) . '/Translate/_files/translation_en.xliff', 'en');
            $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Xliff);
        } catch (Zend_Translate_Exception $e) {
            // success - not implemented
        }

        try {
            $lang = new Zend_Translate('tbx' , dirname(__FILE__) . '/Translate/_files/translation_en.xliff', 'en');
            $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Xliff);
        } catch (Zend_Translate_Exception $e) {
            // success - not implemented
        }

        try {
            $lang = new Zend_Translate('xmltm' , dirname(__FILE__) . '/Translate/_files/translation_en.xliff', 'en');
            $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Xliff);
        } catch (Zend_Translate_Exception $e) {
            // success - not implemented
        }

        try {
            $lang = new Zend_Translate('noadapter' , dirname(__FILE__) . '/Translate/_files/translation_en.xliff', 'en');
            $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Xliff);
        } catch (Zend_Translate_Exception $e) {
            // success - not implemented
        }
    }

    public function testSetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/_files/testmsg_en.mo', 'en');

        $lang->setAdapter(Zend_Translate::AN_ARRAY, array());
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);
    }

    public function testAddTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');

        $this->assertEquals($lang->_('msg2'), 'msg2');

        $lang->addTranslation(array('msg2' => 'Message 2'), 'en');
        $this->assertEquals($lang->_('msg2'), 'Message 2');
        $this->assertEquals($lang->_('msg3'), 'msg3');

        $lang->addTranslation(array('msg3' => 'Message 3'), 'en', array('clear' => true));
        $this->assertEquals($lang->_('msg2'), 'msg2');
        $this->assertEquals($lang->_('msg3'), 'Message 3');
    }

    public function testGetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals($lang->getLocale(), 'en');
    }

    public function testSetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');

        $this->assertEquals($lang->getLocale(), 'en');

        $lang->setLocale('ru');
        $this->assertEquals($lang->getLocale(), 'ru');
    }

    public function testSetLanguage()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');

        $this->assertEquals($lang->getLocale(), 'en');

        $lang->setLocale('ru');
        $this->assertEquals($lang->getLocale(), 'ru');
    }

    public function testGetLanguageList()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');

        $this->assertEquals(count($lang->getList()), 2);
        $this->assertTrue(in_array('en', $lang->getList()));
        $this->assertTrue(in_array('ru', $lang->getList()));
    }

    public function testIsAvailable()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');

        $this->assertTrue($lang->isAvailable('en'));
        $this->assertTrue($lang->isAvailable('ru'));
        $this->assertFalse($lang->isAvailable('fr'));
    }

    public function testTranslate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');

        $this->assertEquals($lang->_('msg1'), 'Message 1 (en)');
        $this->assertEquals($lang->_('msg1', 'ru'), 'Message 1 (ru)');

        $this->assertEquals($lang->_('msg2'), 'msg2');
        $this->assertEquals($lang->_('msg2', 'ru'), 'msg2');

        $this->assertEquals($lang->translate('msg1'), 'Message 1 (en)');
        $this->assertEquals($lang->translate('msg1', 'ru'), 'Message 1 (ru)');

        $this->assertEquals($lang->translate('msg2'), 'msg2');
        $this->assertEquals($lang->translate('msg2', 'ru'), 'msg2');
    }

    public function testIsTranslated()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en_US');

        $this->assertEquals($lang->isTranslated('msg1'), true);
        $this->assertEquals($lang->isTranslated('msg2'), false);
        $this->assertEquals($lang->isTranslated('msg1',false,'en'), false);
        $this->assertEquals($lang->isTranslated('msg1',true,'en'), false);
        $this->assertEquals($lang->isTranslated('msg1',false,'ru'), false);
    }
}
