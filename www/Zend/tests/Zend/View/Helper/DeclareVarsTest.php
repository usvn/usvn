<?php
require_once 'Zend/View.php';
require_once 'Zend/View/Helper/DeclareVars.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_View_Helper_DeclareVarsTest extends PHPUnit_Framework_TestCase 
{
    public function setUp()
    {
        $view = new Zend_View();
        $base = str_replace('/', DIRECTORY_SEPARATOR, '/../_templates');
        $view->setScriptPath(dirname(__FILE__) . $base);
        $view->strictVars(true);
        $this->view = $view;
    }

    public function tearDown()
    {
        unset($this->view);
    }

    protected function _declareVars()
    {
        $this->view->declareVars(
            'varName1',
            'varName2',
            array(
                'varName3' => 'defaultValue',
                'varName4' => array()
            )
        );
    }

    public function testDeclareUndeclaredVars()
    {
        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));

        $this->assertEquals('defaultValue', $this->view->varName3);
        $this->assertEquals(array(), $this->view->varName4);
    }

    public function testDeclareDeclaredVars()
    {
        $this->view->varName2 = 'alreadySet';
        $this->view->varName3 = 'myValue';
        $this->view->varName5 = 'additionalValue';

        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));
        $this->assertTrue(isset($this->view->varName5));

        $this->assertEquals('alreadySet', $this->view->varName2);
        $this->assertEquals('myValue', $this->view->varName3);
        $this->assertEquals('additionalValue', $this->view->varName5);
    }
}
