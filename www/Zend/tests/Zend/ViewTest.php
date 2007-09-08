<?php
/**
 * @package    Zend_View
 * @subpackage UnitTests
 */


/** Zend_View */
require_once 'Zend/View.php';

/** Zend_View_Interface */
require_once 'Zend/View/Interface.php';


/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_ViewTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->errorReporting = error_reporting();
        $this->displayErrors  = ini_get('display_errors');
    }

    public function tearDown()
    {
        error_reporting($this->errorReporting);
        ini_set('display_errors', $this->displayErrors);
    }

	/**
	 * Tests that the default script path is properly initialized
	 */
	public function testDefaultScriptPath()
	{
		$this->_testDefaultPath('script', false);
	}

    public function normalizePath($path)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }
    
    
	/**
	 * Tests that the default helper path is properly initialized
	 * and the directory is readable
	 */
	public function testDefaultHelperPath()
	{	
		$this->_testDefaultPath('helper');
	}

	
	/**
	 * Tests that the default filter path is properly initialized
	 * and the directory is readable
	 */
	public function testDefaultFilterPath()
	{
		$this->_testDefaultPath('filter');
	}


	/**
	 * Tests that script paths are added, properly ordered, and that
	 * directory separators are handled correctly.
	 */
    public function testAddScriptPath()
    {
    	$this->_testAddPath('script');
    }
    
    
	/**
	 * Tests that helper paths are added, properly ordered, and that
	 * directory separators are handled correctly.
	 */
    public function testAddHelperPath()
    {
    	$this->_testAddPath('helper');
    }
    
    
	/**
	 * Tests that filter paths are added, properly ordered, and that
	 * directory separators are handled correctly.
	 */
    public function testAddFilterPath()
    {
    	$this->_testAddPath('filter');
    }
    

	/**
	 * Tests that the (script|helper|filter) path array is properly
	 * initialized after instantiation.
	 * 
	 * @param string  $pathType         one of "script", "helper", or "filter".
	 * @param boolean $testReadability  check if the path is readable?
	 */
    protected function _testDefaultPath($pathType, $testReadability = true)
    {
    	$view = new Zend_View();
    	
		$reflector = $view->getAllPaths();
        $paths     = $reflector[$pathType];
			
		// test default helper path
		$this->assertType('array', $paths);
        if ('script' == $pathType) {
            $this->assertEquals(0, count($paths));
        } else {
            $this->assertEquals(1, count($paths));
            $item = $paths[0];

            $prefix = 'Zend_View_' . ucfirst($pathType) . '_';
            $this->assertEquals($prefix, $item['prefix']);

            if ($testReadability) {
                $this->assertTrue(is_dir($item['dir']));
                $this->assertTrue(is_readable($item['dir']));	    	
            }
        }
    }
        
    
    /**
     * Tests (script|helper|filter) paths can be added, that they are added
     * in the proper order, and that directory separators are properly handled. 
     * 
     * @param string $pathType one of "script", "helper", or "filter".
     */
    protected function _testAddPath($pathType)
    {
    	$view   = new Zend_View();
        $prefix = 'Zend_View_' . ucfirst($pathType) . '_';

    	// introspect default paths and build expected results.
		$reflector     = $view->getAllPaths(); 
		$expectedPaths = $reflector[$pathType];

        if ('script' == $pathType) {
            array_unshift($expectedPaths, 'baz' . DIRECTORY_SEPARATOR);
            array_unshift($expectedPaths, 'bar' . DIRECTORY_SEPARATOR);
            array_unshift($expectedPaths, 'foo' . DIRECTORY_SEPARATOR);
        } else {
            array_unshift($expectedPaths, array('prefix' => $prefix, 'dir' => 'baz' . DIRECTORY_SEPARATOR));
            array_unshift($expectedPaths, array('prefix' => $prefix, 'dir' => 'bar' . DIRECTORY_SEPARATOR));
            array_unshift($expectedPaths, array('prefix' => $prefix, 'dir' => 'foo' . DIRECTORY_SEPARATOR));
        }
    	
    	// add paths
    	$func = 'add' . ucfirst($pathType) . 'Path';
		$view->$func('baz');    // no separator
		$view->$func('bar\\');  // windows 
		$view->$func('foo/');   // unix		
		
    	// introspect script paths after adding two new paths
		$reflector   = $view->getAllPaths(); 
		$actualPaths = $reflector[$pathType];

        $this->assertSame($expectedPaths, $actualPaths);
    }

    
	/**
	 * Tests that the Zend_View environment is clean of any instance variables
	 */
    public function testSandbox()
    {
    	$view = new Zend_View();
    	$this->assertSame(array(), get_object_vars($view));
    }

    
    /**
     * Tests that isset() and empty() work correctly.  This is a common problem
     * because __isset() was not supported until PHP 5.1.
     */
    public function testIssetEmpty()
    {
    	$view = new Zend_View();
		$this->assertFalse(isset($view->foo));    	
		$this->assertTrue(empty($view->foo));
		
		$view->foo = 'bar';
		$this->assertTrue(isset($view->foo));
		$this->assertFalse(empty($view->foo));
    }    
    
    
    /**
     * Tests that a help can be loaded from the search path 
     *
     */
    public function testLoadHelper()
    {
		$view = new Zend_View();
		
		$view->setHelperPath(array(dirname(__FILE__) . '/View/_stubs/HelperDir1',
								   dirname(__FILE__) . '/View/_stubs/HelperDir2'));
								   

		$this->assertEquals('foo', $view->stub1());
		$this->assertEquals('bar', $view->stub2());

		// erase the paths to the helper stubs
		$view->setHelperPath(null);

		// verify that object handle of a stub was cache by calling it again
		// without its path in the helper search paths
		$this->assertEquals( 'foo', $view->stub1() );	
    }
    
    
    /**
     * Tests that calling a nonexistant helper file throws the expected exception
     */
    public function testLoadHelperNonexistantFile()
    {
    	$view = new Zend_View();
    	
    	try {
	    	$view->nonexistantHelper();
    	} catch (Zend_View_Exception $e) {
    		$this->assertRegexp('/helper [\'a-z]+ not found in path/i', $e->getMessage());
    		return;
    	}
    }

    
    /**
     * Tests that calling a helper whose file exists but class is not found within
     * throws the expected exception
     */
    public function testLoadHelperNonexistantClass()
    {
    	$view = new Zend_View();
    	
		$view->setHelperPath(array(dirname(__FILE__) . '/View/_stubs/HelperDir1'));

		
		try {
			// attempt to load the helper StubEmpty, whose file exists but 
			// does not contain the expected class within
			$view->stubEmpty();	
		} catch (Zend_View_Exception $e) {
    		$this->assertRegexp("/['_a-z]+ not found in path/i", $e->getMessage());
		}
    }
    
    
    
    /**
     * Tests that render() can render a template.
     */
    public function testRender()
    {
    	$view = new Zend_View();
    	
    	$view->setScriptPath(dirname(__FILE__) . '/View/_templates');
    	
    	$view->bar = 'bar';
    	
    	$this->assertEquals("foo bar baz\n", $view->render('test.phtml') );
    }
    
    /**
     * Tests that render() works when called within a template, and that 
     * protected members are not available
     */
    public function testRenderSubTemplates()
    {
        $view = new Zend_View();
        $view->setScriptPath(dirname(__FILE__) . '/View/_templates');
        $view->content = 'testSubTemplate.phtml';
        $this->assertEquals('', $view->render('testParent.phtml'));

        $logFile = dirname(__FILE__) . '/View/_templates/view.log';
        $this->assertTrue(file_exists($logFile));
        $log = file_get_contents($logFile);
        unlink($logFile); // clean up...
        $this->assertContains('This text should not be displayed', $log);
        $this->assertNotContains('testSubTemplate.phtml', $log);
    }
    
    /**
     * Tests that array properties may be modified after being set (see [ZF-460] 
     * and [ZF-268] for symptoms leading to this test)
     */
    public function testSetArrayProperty()
    {
        $view = new Zend_View();
        $view->foo   = array();
        $view->foo[] = 42;

        $foo = $view->foo;

        $this->assertTrue(is_array($foo));
        $this->assertEquals(42, $foo[0], var_export($foo, 1));

        $view->assign('bar', array());
        $view->bar[] = 'life';
        $bar = $view->bar;
        $this->assertTrue(is_array($bar));
        $this->assertEquals('life', $bar[0], var_export($bar, 1));

        $view->assign(array(
            'baz' => array('universe'),
        ));
        $view->baz[] = 'everything';
        $baz = $view->baz;
        $this->assertTrue(is_array($baz));
        $this->assertEquals('universe', $baz[0]);
        $this->assertEquals('everything', $baz[1], var_export($baz, 1));
    }

    /**
     * Test that array properties are cleared following clearVars() call
     */
    public function testClearVars()
    {
        $view = new Zend_View();
        $view->foo     = array();
        $view->content = 'content';

        $this->assertTrue(is_array($view->foo));
        $this->assertEquals('content', $view->content);

        $view->clearVars();
        $this->assertFalse(isset($view->foo));
        $this->assertFalse(isset($view->content));
    }

    /**
     * Test that script paths are cleared following setScriptPath(null) call
     */
    public function testClearScriptPath()
    {
        $view = new Zend_View();
        
        // paths should be initially empty
        $this->assertSame(array(), $view->getScriptPaths());

        // add a path
        $view->setScriptPath('foo');
        $scriptPaths = $view->getScriptPaths();
        $this->assertType('array', $scriptPaths);
        $this->assertEquals(1, count($scriptPaths));
        
        // clear paths
        $view->setScriptPath(null);
        $this->assertSame(array(), $view->getScriptPaths());
    }

    /**
     * Test that an exception is thrown when no script path is set
     */
    public function testNoPath()
    {
        $view = new Zend_View();
        try {
            $view->render('somefootemplate.phtml');
            $this->fail('Rendering a template when no script path is set should raise an exception');
        } catch (Exception $e) {
            // success...
        }
    }

    /**
     * Test that getEngine() returns the same object
     */
    public function testGetEngine()
    {
        $view = new Zend_View();
        $this->assertSame($view, $view->getEngine());
    }

    public function testInstanceOfInterface()
    {
        $view = new Zend_View();
        $this->assertTrue($view instanceof Zend_View_Interface);
    }

    public function testGetVars()
    {
        $view = new Zend_View();
        $view->foo = 'bar';
        $view->bar = 'baz';
        $view->baz = array('foo', 'bar');

        $vars = $view->getVars();
        $this->assertEquals(3, count($vars));
        $this->assertEquals('bar', $vars['foo']);
        $this->assertEquals('baz', $vars['bar']);
        $this->assertEquals(array('foo', 'bar'), $vars['baz']);
    }

    /**
     * Test set/getEncoding() 
     */
    public function testSetGetEncoding()
    {
        $view = new Zend_View();
        $this->assertEquals('ISO-8859-1', $view->getEncoding());

        $view->setEncoding('UTF-8');
        $this->assertEquals('UTF-8', $view->getEncoding());
    }

    public function testEmptyPropertiesReturnAppropriately()
    {
        $view = new Zend_View();
        $view->foo = false;
        $view->bar = null;
        $view->baz = '';
        $this->assertTrue(empty($view->foo));
        $this->assertTrue(empty($view->bar));
        $this->assertTrue(empty($view->baz));
    }

    public function testFluentInterfaces()
    {
        $view = new Zend_View();
        try {
            $test = $view->setEscape('strip_tags')
                ->setFilter('htmlspecialchars')
                ->setEncoding('UTF-8')
                ->setScriptPath(dirname(__FILE__) . '/View/_templates')
                ->setHelperPath(dirname(__FILE__) . '/View/_stubs/HelperDir1')
                ->setFilterPath(dirname(__FILE__) . '/View/_stubs/HelperDir1')
                ->assign('foo', 'bar');
        } catch (Exception $e){
            $this->fail('Setters should not throw exceptions');
        }

        $this->assertTrue($test instanceof Zend_View);
    }

    public function testSetConfigInConstructor()
    {
        $scriptPath = $this->normalizePath(dirname(__FILE__) . '/View/_templates/');
        $helperPath = $this->normalizePath(dirname(__FILE__) . '/View/_stubs/HelperDir1/');
        $filterPath = $this->normalizePath(dirname(__FILE__) . '/View/_stubs/HelperDir1/');

        $config = array(
            'escape'           => 'strip_tags',
            'encoding'         => 'UTF-8',
            'scriptPath'       => $scriptPath,
            'helperPath'       => $helperPath,
            'helperPathPrefix' => 'My_View_Helper',
            'filterPath'       => $filterPath,
            'filterPathPrefix' => 'My_View_Filter',
            'filter'           => 'urlencode',
        );

        $view = new Zend_View($config);
        $scriptPaths = $view->getScriptPaths();
        $helperPaths = $view->getHelperPaths();
        $filterPaths = $view->getFilterPaths();

        $this->assertContains($this->normalizePath($scriptPath), $scriptPaths);

        $found  = false;
        $prefix = false;
        foreach ($helperPaths as $pathInfo) {
            if (strstr($pathInfo['dir'], $helperPath)) {
                $found  = true;
                $prefix = $pathInfo['prefix'];
            }
        }
        $this->assertTrue($found, var_export($helperPaths, 1));
        $this->assertEquals('My_View_Helper_', $prefix);

        $found  = false;
        $prefix = false;
        foreach ($filterPaths as $pathInfo) {
            if (strstr($pathInfo['dir'], $filterPath)) {
                $found  = true;
                $prefix = $pathInfo['prefix'];
            }
        }
        $this->assertTrue($found, var_export($filterPaths, 1));
        $this->assertEquals('My_View_Filter_', $prefix);
    }

    public function testUnset()
    {
        $view = new Zend_View();
        unset($view->_path);
    }

    public function testSetProtectedThrowsException()
    {
        $view = new Zend_View();
        try {
            $view->_path = 'bar';
            $this->fail('Should not be able to set protected properties');
        } catch (Exception $e) {
            // success
        }
    }

    public function testHelperPathWithPrefix()
    {
        $view = new Zend_View();
        $status = $view->addHelperPath(dirname(__FILE__) . '/View/_stubs/HelperDir1/', 'My_View_Helper');
        $this->assertSame($view, $status);
        $helperPaths = $view->getHelperPaths();
        $path = $helperPaths[0];
        $this->assertEquals('My_View_Helper_', $path['prefix']);
        $this->assertEquals($this->normalizePath(dirname(__FILE__) . '/View/_stubs/HelperDir1/'), $path['dir']);

        $view->setHelperPath(dirname(__FILE__) . '/View/_stubs/HelperDir2/', 'Other_View_Helper');
        $helperPaths = $view->getHelperPaths();
        $path = $helperPaths[0];
        $this->assertEquals('Other_View_Helper_', $path['prefix']);
        $this->assertEquals($this->normalizePath(dirname(__FILE__) . '/View/_stubs/HelperDir2/'), $path['dir']);
    }

    public function testHelperPathWithPrefixAndRelativePath()
    {
        $view = new Zend_View();
        $status = $view->addHelperPath('Zend/View/_stubs/HelperDir1/', 'My_View_Helper');
        $this->assertSame($view, $status);
        $helperPaths = $view->getHelperPaths();
        $path = $helperPaths[0];
        $this->assertEquals('My_View_Helper_', $path['prefix']);
        $this->assertContains($this->normalizePath('Zend/View/_stubs/HelperDir1/'), $path['dir']);
    }

    public function testFilterPathWithPrefix()
    {
        $view = new Zend_View();
        $status = $view->addFilterPath(dirname(__FILE__) . '/View/_stubs/HelperDir1/', 'My_View_Filter');
        $this->assertSame($view, $status);
        $filterPaths = $view->getFilterPaths();
        $path = $filterPaths[0];
        $this->assertEquals('My_View_Filter_', $path['prefix']);
        $this->assertEquals($this->normalizePath(dirname(__FILE__) . '/View/_stubs/HelperDir1/'), $path['dir']);

        $view->setFilterPath(dirname(__FILE__) . '/View/_stubs/HelperDir2/', 'Other_View_Filter');
        $filterPaths = $view->getFilterPaths();
        $path = $filterPaths[0];
        $this->assertEquals('Other_View_Filter_', $path['prefix']);
        $this->assertEquals($this->normalizePath(dirname(__FILE__) . '/View/_stubs/HelperDir2/'), $path['dir']);
    }

    public function testAssignThrowsExceptionsOnBadValues()
    {
        $view = new Zend_View();
        try {
            $view->assign('_path', dirname(__FILE__) . '/View/_stubs/HelperDir2/');
            $this->fail('Protected/private properties cannot be assigned');
        } catch (Exception $e) {
            // success
        }

        try {
            $view->assign(array('_path' => dirname(__FILE__) . '/View/_stubs/HelperDir2/'));
            $this->fail('Protected/private properties cannot be assigned');
        } catch (Exception $e) {
            // success
        }

        try {
            $view->assign($this);
            $this->fail('Assign spec requires string or array');
        } catch (Exception $e) {
            // success
        }
    }

    public function testEscape()
    {
        $view = new Zend_View();
        $original = "Me, Myself, & I";
        $escaped  = $view->escape($original);
        $this->assertNotEquals($original, $escaped);
        $this->assertEquals("Me, Myself, &amp; I", $escaped);
    }

    public function testCustomEscape()
    {
        $view = new Zend_View();
        $view->setEscape('strip_tags');
        $original = "<p>Some text</p>";
        $escaped  = $view->escape($original);
        $this->assertNotEquals($original, $escaped);
        $this->assertEquals("Some text", $escaped);
    }

    public function testZf995UndefinedPropertiesReturnNull()
    {
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', true);
    	$view = new Zend_View();
    	$view->setScriptPath(dirname(__FILE__) . '/View/_templates');

        ob_start();
        echo $view->render('testZf995.phtml');
        $content = ob_get_flush();
        $this->assertTrue(empty($content));
    }

    public function testInit()
    {
        $view = new Zend_ViewTest_Extension();
        $this->assertEquals('bar', $view->foo);
        $paths = $view->getScriptPaths();
        $this->assertEquals(1, count($paths));
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . '_templates' . DIRECTORY_SEPARATOR, $paths[0]);
    }

    public function testHelperViewAccessor()
    {
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/View/_stubs/HelperDir2/');
        $view->stub2();

        $helpers = $view->getHelpers();
        $this->assertEquals(1, count($helpers));
        $this->assertTrue(isset($helpers['stub2']));
        $stub2 = $helpers['stub2'];
        $this->assertTrue($stub2 instanceof Zend_View_Helper_Stub2);
        $this->assertTrue(isset($stub2->view));
        $this->assertSame($view, $stub2->view);
    }

    public function testSetBasePath()
    {
        $view = new Zend_View();
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View';
        $view->setBasePath($base);
        $this->_testBasePath($view, $base);
    }

    public function testAddBasePath()
    {
        $view = new Zend_View();
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View';
        $view->addBasePath($base);
        $this->_testBasePath($view, $base);

        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View2';
        $view->addBasePath($base);
        $this->_testBasePath($view, $base);
    }

    public function testAddBasePathWithClassPrefix()
    {
        $view = new Zend_View();
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View';
        $view->addBasePath($base, 'My_Foo');
        $this->_testBasePath($view, $base, 'My_Foo');
    }

    public function testSetBasePathFromConstructor()
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View';
        $view = new Zend_View(array('basePath' => $base));
        $this->_testBasePath($view, $base);
    }

    public function testSetBasePathWithClassPrefix()
    {
        $view = new Zend_View();
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View';
        $view->setBasePath($base, 'My_Foo');
        $this->_testBasePath($view, $base, 'My_Foo');
    }

    public function testSetBasePathFromConstructorWithClassPrefix()
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View';
        $view = new Zend_View(array('basePath' => $base, 'basePathPrefix' => 'My_Foo'));
        $this->_testBasePath($view, $base);
    }

    protected function _testBasePath(Zend_View $view, $base, $classPrefix = null)
    {
        $scriptPaths = $view->getScriptPaths();
        $helperPaths = $view->getHelperPaths();
        $filterPaths = $view->getFilterPaths();
        $this->assertContains($base . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR, $scriptPaths);

        $found  = false;
        $prefix = false;
        foreach ($helperPaths as $path) {
            if ($path['dir'] == $base . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR) {
                $found  = true;
                $prefix = $path['prefix'];
                break;
            }
        }
        $this->assertTrue($found, var_export($helperPaths, 1));
        if (null !== $classPrefix) {
            $this->assertTrue($prefix !== false);
            $this->assertEquals($classPrefix . '_Helper_', $prefix);
        }

        $found  = false;
        $prefix = false;
        foreach ($filterPaths as $path) {
            if ($path['dir'] == $base . DIRECTORY_SEPARATOR . 'filters' . DIRECTORY_SEPARATOR) {
                $found  = true;
                $prefix = $path['prefix'];
                break;
            }
        }
        $this->assertTrue($found, var_export($filterPaths, 1));
        if (null !== $classPrefix) {
            $this->assertTrue($prefix !== false);
            $this->assertEquals($classPrefix . '_Filter_', $prefix);
        }
    }

    public function testStrictVars()
    {
        $view = new Zend_View();
        $view->setScriptPath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . '_templates');
        $view->strictVars(true);
        $content = $view->render('testStrictVars.phtml');
        $this->assertContains('Key "foo" does not exist', $content, $content);
        $this->assertContains('Key "bar" does not exist', $content);
    }

    public function testGetScriptPath()
    {
        $view = new Zend_View();
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . '_templates';
        $view->setScriptPath($base);
        $path = $view->getScriptPath('test.phtml');
        $this->assertEquals($base . DIRECTORY_SEPARATOR . 'test.phtml', $path);
    }

    public function testGetHelper()
    {
        // require so we can do type hinting
        require_once 'Zend/View/Helper/DeclareVars.php';
        $view = new Zend_View();
        $view->declareVars();
        $helper = $view->getHelper('declareVars');
        $this->assertTrue($helper instanceof Zend_View_Helper_DeclareVars);
    }

    public function testGetHelperPath()
    {
        require_once 'Zend/View/Helper/DeclareVars.php';
        $reflection = new ReflectionClass('Zend_View_Helper_DeclareVars');
        $expected   = $reflection->getFileName();

        $view = new Zend_View();
        $view->declareVars();
        $helperPath = $view->getHelperPath('declareVars');
        $this->assertEquals($expected, $helperPath);
    }

    public function testGetFilter()
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR;
        require_once $base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1' . DIRECTORY_SEPARATOR . 'Foo.php';

        $view = new Zend_View();
        $view->setScriptPath($base . '_templates');
        $view->addFilterPath($base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1');

        $filter = $view->getFilter('foo');
        $this->assertTrue($filter instanceof Zend_View_Filter_Foo);
    }

    public function testGetFilterPath()
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR;
        $expected = $base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1' . DIRECTORY_SEPARATOR . 'Foo.php';

        $view = new Zend_View();
        $view->setScriptPath($base . '_templates');
        $view->addFilterPath($base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1');

        $filterPath = $view->getFilterPath('foo');
        $this->assertEquals($expected, $filterPath);
    }

    public function testGetFilters()
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR;

        $view = new Zend_View();
        $view->setScriptPath($base . '_templates');
        $view->addFilterPath($base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1');
        $view->addFilter('foo');

        $filters = $view->getFilters();
        $this->assertEquals(1, count($filters));
        $this->assertEquals('foo', $filters[0]);
    }

    public function testMissingViewScriptExceptionText()
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR;
        $view = new Zend_View();
        $view->setScriptPath($base . '_templates');

        try {
            $view->render('bazbatNotExists.php.tpl');
            $this->fail('Non-existent view script should cause an exception');
        } catch (Exception $e) {
            $this->assertContains($base. '_templates', $e->getMessage());
        }
    }
}

class Zend_ViewTest_Extension extends Zend_View
{
    public function init()
    {
        $this->assign('foo', 'bar');
        $this->setScriptPath(dirname(__FILE__) . '/View/_templates');
    }
}
