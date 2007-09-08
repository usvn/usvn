<?php
if (!defined('PHPUnit_MAIN_METHOD')) {

    define('PHPUnit_MAIN_METHOD', 'Zend_View_AllTests::main');

    set_include_path(
        dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
        . dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
        . get_include_path()
    );
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Helper/DeclareVarsTest.php';
require_once 'Helper/FormCheckboxTest.php';
require_once 'Helper/FormLabelTest.php';
require_once 'Helper/FormTextTest.php';
require_once 'Helper/HtmlListTest.php';

class Zend_View_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_View');

        $suite->addTestSuite('Zend_View_Helper_DeclareVarsTest');
        $suite->addTestSuite('Zend_View_Helper_FormCheckboxTest');
        $suite->addTestSuite('Zend_View_Helper_FormLabelTest');
        $suite->addTestSuite('Zend_View_Helper_FormTextTest');
        $suite->addTestSuite('Zend_View_Helper_HtmlListTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_AllTests::main') {
    Zend_View_AllTests::main();
}
