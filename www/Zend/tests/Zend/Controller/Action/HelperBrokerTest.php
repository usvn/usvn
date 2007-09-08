<?php
// Call Zend_Controller_Action_HelperBrokerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_HelperBrokerTest::main");

    $basePath = realpath(dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 4));

    set_include_path(
        $basePath . DIRECTORY_SEPARATOR . 'tests'
        . PATH_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . 'library'
        . PATH_SEPARATOR . get_include_path()
    );
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
require_once 'Zend/Controller/Action/Helper/Redirector.php';

class Zend_Controller_Action_HelperBrokerTest extends PHPUnit_Framework_TestCase
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_HelperBrokerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    public function setUp()
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setParam('noViewRenderer', true)
                    ->setParam('noErrorHandler', true);
        Zend_Controller_Action_HelperBroker::resetHelpers();
    }
    
    public function testLoadingAndReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());
        
        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', $response->getBody());
    }

    public function testLoadingAndReturningHelperStatically()
    {
        $helper = new Zend_Controller_Action_HelperBroker_TestHelper();
        Zend_Controller_Action_HelperBroker::addHelper($helper);
        $received = Zend_Controller_Action_HelperBroker::getExistingHelper('testHelper');
        $this->assertSame($received, $helper);
    }

    public function testGetExistingHelperThrowsExceptionWithUnregisteredHelper()
    {
        try {
            $received = Zend_Controller_Action_HelperBroker::getExistingHelper('testHelper');
            $this->fail('Retrieving unregistered helpers should throw an exception');
        } catch (Exception $e) {
            // success
        }
    }

    public function testLoadingHelperOnlyInitializesOnce()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('default')
                ->setControllerName('zend_controller_action_helper-broker')
                ->setActionName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->front->setResponse($response);

        $helper = new Zend_Controller_Action_HelperBroker_TestHelper();
        Zend_Controller_Action_HelperBroker::addHelper($helper);

        $controller = new Zend_Controller_Action_HelperBrokerController($request, $response, array());
        $controller->test();
        $received = $controller->getHelper('testHelper');
        $this->assertSame($helper, $received);
        $this->assertEquals(1, $helper->count);
    }

    public function testLoadingAndCheckingHelpersStatically()
    {
        $helper = new Zend_Controller_Action_Helper_Redirector();
        Zend_Controller_Action_HelperBroker::addHelper($helper);

        $this->assertTrue(Zend_Controller_Action_HelperBroker::hasHelper('redirector'));
    }
    
    public function testLoadingAndRemovingHelpersStatically()
    {
        $helper = new Zend_Controller_Action_Helper_Redirector();
        Zend_Controller_Action_HelperBroker::addHelper($helper);

        $this->assertTrue(Zend_Controller_Action_HelperBroker::hasHelper('redirector'));
        Zend_Controller_Action_HelperBroker::removeHelper('redirector'); 
        $this->assertFalse(Zend_Controller_Action_HelperBroker::hasHelper('redirector'));
    }
     public function testReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());
        
        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', $response->getBody());
    }

    public function testReturningHelperViaMagicGet()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-helper-via-magic-get/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());
        
        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', $response->getBody());
    }
    
    public function testReturningHelperViaMagicCall()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-helper-via-magic-call/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());
        
        $this->front->returnResponse(true);
        
        require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        Zend_Controller_Action_HelperBroker::addHelper(new MyApp_TestHelper());
        
        $response = $this->front->dispatch($request);
        $this->assertEquals('running direct call', $response->getBody());
    }
    
    public function testNonExistentHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-bad-helper/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());
        
        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Action Helper by name NonExistentHelper not found.', $response->getBody());
    }
    
    public function testCustomHelperRegistered()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());
        
        $this->front->returnResponse(true);
        
        require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        Zend_Controller_Action_HelperBroker::addHelper(new MyApp_TestHelper());
        
        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp_TestHelper', $response->getBody());
    }
        
    public function testCustomHelperFromPath()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());
        
        $this->front->returnResponse(true);
        
        Zend_Controller_Action_HelperBroker::addPath(
            dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Helpers',
            'MyApp'
            );
        
        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp_TestHelper', $response->getBody());
    }

    public function testGetExistingHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_Redirector());
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_ViewRenderer());

        $helpers = Zend_Controller_Action_HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertEquals(2, count($helpers));
        $this->assertContains('ViewRenderer', array_keys($helpers));
        $this->assertContains('Redirector', array_keys($helpers));
    }

    public function testGetHelperStatically()
    {
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->assertTrue($helper instanceof Zend_Controller_Action_Helper_ViewRenderer);

        $helpers = Zend_Controller_Action_HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertEquals(1, count($helpers));
    }
}

class Zend_Controller_Action_HelperBroker_TestHelper extends Zend_Controller_Action_Helper_Abstract
{
    public $count = 0;

    public function init()
    {
        ++$this->count;
    }
}

class Zend_Controller_Action_HelperBrokerController extends Zend_Controller_Action
{
    public $helper;

    public function init()
    {
        $this->helper = $this->_helper->getHelper('testHelper');
    }

    public function test()
    {
        $this->_helper->getHelper('testHelper');
    }
}

// Call Zend_Controller_Action_HelperBrokerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_HelperBrokerTest::main") {
    Zend_Controller_Action_HelperBrokerTest::main();
}
