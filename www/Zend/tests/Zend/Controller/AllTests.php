<?php
if (!defined('PHPUnit_MAIN_METHOD')) {

    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_AllTests::main');

    set_include_path(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
                 . get_include_path());
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'ActionTest.php';
require_once 'Action/HelperBrokerTest.php';
require_once 'Action/Helper/FlashMessengerTest.php';
require_once 'Action/Helper/RedirectorTest.php';
require_once 'Action/Helper/ViewRendererTest.php';
require_once 'Dispatcher/StandardTest.php';
require_once 'FrontTest.php';
require_once 'Plugin/BrokerTest.php';
require_once 'Plugin/ErrorHandlerTest.php';
require_once 'Request/Apache404Test.php';
require_once 'Request/HttpTest.php';
require_once 'Response/HttpTest.php';
require_once 'Router/RouteTest.php';
require_once 'Router/Route/ModuleTest.php';
require_once 'Router/Route/RegexTest.php';
require_once 'Router/Route/StaticTest.php';
require_once 'Router/RewriteTest.php';


class Zend_Controller_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Controller');

        $suite->addTestSuite('Zend_Controller_ActionTest');
        $suite->addTestSuite('Zend_Controller_Action_HelperBrokerTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_FlashMessengerTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_RedirectorTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_ViewRendererTest');
        $suite->addTestSuite('Zend_Controller_Dispatcher_StandardTest');
        $suite->addTestSuite('Zend_Controller_FrontTest');
        $suite->addTestSuite('Zend_Controller_Plugin_BrokerTest');
        $suite->addTestSuite('Zend_Controller_Plugin_ErrorHandlerTest');
        $suite->addTestSuite('Zend_Controller_Request_Apache404Test');
        $suite->addTestSuite('Zend_Controller_Request_HttpTest');
        $suite->addTestSuite('Zend_Controller_Response_HttpTest');
        $suite->addTestSuite('Zend_Controller_Router_RouteTest');
        $suite->addTestSuite('Zend_Controller_Router_Route_ModuleTest');
        $suite->addTestSuite('Zend_Controller_Router_Route_RegexTest');
        $suite->addTestSuite('Zend_Controller_Router_Route_StaticTest');
        $suite->addTestSuite('Zend_Controller_Router_RewriteTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Controller_AllTests::main') {
    Zend_Controller_AllTests::main();
}
