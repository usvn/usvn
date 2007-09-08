<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Rewrite */
require_once 'Zend/Controller/Router/Rewrite.php';

/** Zend_Controller_Dispatcher_Standard */
require_once 'Zend/Controller/Dispatcher/Standard.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'PHPUnit/Runner/Version.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_RewriteTest extends PHPUnit_Framework_TestCase
{
    protected $_router;
    
    public function setUp() {
        $this->_router = new Zend_Controller_Router_Rewrite();
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setDispatcher(new Zend_Controller_Router_RewriteTest_Dispatcher());
        $this->_router->setFrontController($front);
    }
    
    public function tearDown() {
        unset($this->_router);
    }

    public function testAddRoute()
    {
        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $routes = $this->_router->getRoutes();

        $this->assertSame(1, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['archive']);

        $this->_router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));
        $routes = $this->_router->getRoutes();

        $this->assertSame(2, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['register']);
    }

    public function testAddRoutes()
    {
        $routes = array(
            'archive' => new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')),
            'register' => new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register'))
        );
        $this->_router->addRoutes($routes);

        $values = $this->_router->getRoutes();

        $this->assertSame(2, count($values));
        $this->assertType('Zend_Controller_Router_Route', $values['archive']);
        $this->assertType('Zend_Controller_Router_Route', $values['register']);
    }
    
    public function testHasRoute()
    {
        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
    
        $this->assertSame(true, $this->_router->hasRoute('archive'));
        $this->assertSame(false, $this->_router->hasRoute('bogus'));
    }

    public function testGetRoute()
    {
        $archive = new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));
        $this->_router->addRoute('archive', $archive);

        $route = $this->_router->getRoute('archive');
    
        $this->assertType('Zend_Controller_Router_Route', $route);
        $this->assertSame($route, $archive);
    }

    public function testRemoveRoute()
    {
        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));

        $route = $this->_router->getRoute('archive');
        
        $this->_router->removeRoute('archive');
    
        $routes = $this->_router->getRoutes();
        $this->assertSame(0, count($routes));

        try {
            $route = $this->_router->removeRoute('archive');
        } catch (Zend_Controller_Router_Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
            return true;
        }

        $this->fail();
    }

    public function testGetNonExistentRoute()
    {
        try {
            $route = $this->_router->getRoute('bogus');
        } catch (Zend_Controller_Router_Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
            return true;
        }

        $this->fail();
    }

    public function testRoute()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request();
        
        $token = $this->_router->route($request);

        $this->assertType('Zend_Controller_Request_Http', $token);
    }

    public function testRouteWithIncorrectRequest()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request_Incorrect();
        
        try {
            $token = $this->_router->route($request);
            $this->fail('Should throw an Exception');
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
        }
    }
    
    public function testDefaultRoute()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request();

        $token = $this->_router->route($request);
        
        $routes = $this->_router->getRoutes();
        $this->assertType('Zend_Controller_Router_Route_Module', $routes['default']);
    }

    public function testDefaultRouteWithEmptyAction()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/ctrl');
        
        $token = $this->_router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('defact', $token->getActionName());
    }

    public function testEmptyRoute()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/');
        
        $this->_router->removeDefaultRoutes();
        $this->_router->addRoute('empty', new Zend_Controller_Router_Route('', array('controller' => 'ctrl', 'action' => 'act')));
        
        $token = $this->_router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }

    public function testEmptyPath()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/');
        
        $this->_router->removeDefaultRoutes();
        $this->_router->addRoute('catch-all', new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'ctrl', 'action' => 'act')));
        
        $token = $this->_router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }

    public function testEmptyPathWithWildcardRoute()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/');
        
        $this->_router->removeDefaultRoutes();
        $this->_router->addRoute('catch-all', new Zend_Controller_Router_Route('*', array('controller' => 'ctrl', 'action' => 'act')));
        
        $token = $this->_router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }

    public function testRouteNotMatched()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/archive/action/bogus');

        $this->_router->addRoute('default', new Zend_Controller_Router_Route(':controller/:action'));
        
        $token = $this->_router->route($request);

        $this->assertNull($token->getControllerName());
        $this->assertNull($token->getActionName());
    }

    public function testDefaultRouteMatched()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/ctrl/act');

        $token = $this->_router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }

    public function testDefaultRouteMatchedWithControllerOnly()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/ctrl');

        $token = $this->_router->route($request);
        
        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('defact', $token->getActionName());
    }

    public function testFirstRouteMatched()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/archive/2006');

        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $this->_router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));

        $token = $this->_router->route($request);

        $this->assertSame('archive', $token->getControllerName());
        $this->assertSame('show', $token->getActionName());
    }

    public function testGetCurrentRoute()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/ctrl/act');

        try {
            $route = $this->_router->getCurrentRoute();
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
        }
        
        try {
            $route = $this->_router->getCurrentRouteName();
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
        }
        
        $token = $this->_router->route($request);

        try {
            $route = $this->_router->getCurrentRoute();
            $name = $this->_router->getCurrentRouteName();
        } catch (Exception $e) {
            $this->fail('Current route is not set');
        }
        
        $this->assertSame('default', $name);
        $this->assertType('Zend_Controller_Router_Route_Module', $route);
    }
    
    public function testAddConfig()
    {
        require_once 'Zend/Config/Ini.php';
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'routes.ini';
        $config = new Zend_Config_Ini($file, 'testing');
        
        $this->_router->addConfig($config, 'routes');
        
        $this->assertType('Zend_Controller_Router_Route_Static', $this->_router->getRoute('news'));
        $this->assertType('Zend_Controller_Router_Route', $this->_router->getRoute('archive'));
        
        try {
            $this->_router->addConfig($config, 'database');
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
            return true;
        }
        
        $this->fail();
        
    }
    
    public function testRemoveDefaultRoutes()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/ctrl/act');
        $this->_router->removeDefaultRoutes();

        $token = $this->_router->route($request);

        $routes = $this->_router->getRoutes();
        $this->assertSame(0, count($routes));
    }
    
    public function testDefaultRouteMatchedWithModules()
    {
        Zend_Controller_Front::getInstance()->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'mod'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin',
        ));
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/mod/ctrl/act');
        $token = $this->_router->route($request);
        
        $this->assertSame('mod',  $token->getModuleName());
        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act',  $token->getActionName());
    }

    public function testRouteCompatDefaults()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/');
        
        $token = $this->_router->route($request);

        $this->assertSame('default', $token->getModuleName());
        $this->assertSame('defctrl', $token->getControllerName());
        $this->assertSame('defact', $token->getActionName());
    }
    
    public function testDefaultRouteWithEmptyControllerAndAction()
    {
        Zend_Controller_Front::getInstance()->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'mod'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin',
        ));
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/mod');
        
        $token = $this->_router->route($request);

        $this->assertSame('mod', $token->getModuleName());
        $this->assertSame('defctrl', $token->getControllerName());
        $this->assertSame('defact', $token->getActionName());
    }
    
    public function testNumericallyIndexedReturnParams()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/archive/2006');

        $this->_router->addRoute('test', new Zend_Controller_Router_Route_Mockup());

        $token = $this->_router->route($request);

        $this->assertSame('index', $token->getControllerName());
        $this->assertSame('index', $token->getActionName());
        $this->assertSame('first_parameter_value', $token->getParam(0));
    }

}

/**
 * Zend_Controller_Router_RewriteTest_Request - request object for router testing
 * 
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_Router_RewriteTest_Request extends Zend_Controller_Request_Http
{
    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://localhost/foo/bar/baz/2';
        }

        parent::__construct($uri);
    }
}

/**
 * Zend_Controller_RouterTest_Dispatcher
 */
class Zend_Controller_Router_RewriteTest_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    public function getDefaultControllerName() 
    {
        return 'defctrl';
    }

    public function getDefaultAction() 
    {
        return 'defact';
    }
}

/**
 * Zend_Controller_RouterTest_Request_Incorrect - request object for router testing
 * 
 * @uses Zend_Controller_Request_Abstract
 */
class Zend_Controller_Router_RewriteTest_Request_Incorrect extends Zend_Controller_Request_Abstract
{
}

class Zend_Controller_Router_Route_Mockup implements Zend_Controller_Router_Route_Interface 
{
    public function match($path) 
    {
        return array(
            "controller" => "index",
            "action" => "index",
            0 => "first_parameter_value"
        );     
    }
    public static function getInstance(Zend_Config $config) {}
    public function assemble($data = array()) {}
}
