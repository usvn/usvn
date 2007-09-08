<?php
// Call Zend_View_Helper_FormTextTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormTextTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/FormText.php';

/**
 * Zend_View_Helper_FormTextTest 
 *
 * Tests formText helper, including some common functionality of all form helpers
 * 
 * @uses PHPUnit_Framework_TestCase
 * @version $Id: FormTextTest.php 4828 2007-05-16 21:28:54Z matthew $
 */
class Zend_View_Helper_FormTextTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormTextTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormText();
        $this->helper->setView($this->view);
    }

    public function testIdSetFromName()
    {
        $element = $this->helper->formText('foo');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="foo"', $element);
    }

    public function testSetIdFromAttribs()
    {
        $element = $this->helper->formText('foo', null, array('id' => 'bar'));
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="bar"', $element);
    }

    public function testSetValue()
    {
        $element = $this->helper->formText('foo', 'bar');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('value="bar"', $element);
    }

    public function testReadOnlyAttribute()
    {
        $element = $this->helper->formText('foo', null, array('readonly' => 'readonly'));
        $this->assertContains('readonly="readonly"', $element);
    }
}

// Call Zend_View_Helper_FormTextTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormTextTest::main") {
    Zend_View_Helper_FormTextTest::main();
}
