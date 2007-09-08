<?php

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */


/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_ConfigTest extends PHPUnit_Framework_TestCase
{
    protected $_iniFileConfig;
    protected $_iniFileNested;

    public function setUp()
    {
        // Arrays representing common config configurations
        $this->_all = array(
            'hostname' => 'all',
            'name' => 'thisname',
            'db' => array(
                'host' => '127.0.0.1',
                'user' => 'username',
                'pass' => 'password',
                'name' => 'live'
                ),
            'one' => array(
                'two' => array(
                    'three' => 'multi'
                    )
                )
            );

        $this->_numericData = array(
             0 => 34,
             1 => 'test',
            );

        $this->_menuData1 = array(
            'button' => array(
                'b0' => array(
                    'L1' => 'button0-1',
                    'L2' => 'button0-2',
                    'L3' => 'button0-3'
                ),
                'b1' => array(
                    'L1' => 'button1-1',
                    'L2' => 'button1-2'
                ),
                'b2' => array(
                    'L1' => 'button2-1'
                    )
                )
            );

        $this->_leadingdot = array('.test' => 'dot-test');
        $this->_invalidkey = array(' ' => 'test', ''=>'test2');

    }

    public function testLoadSingleSection()
    {
        $config = new Zend_Config($this->_all, false);

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull($config->nonexistent); // property doesn't exist
    }

    public function testIsset()
    {
        if (version_compare(PHP_VERSION, '5.1', '>=')) {
            $config = new Zend_Config($this->_all, false);

            $this->assertFalse(isset($config->notarealkey));
            $this->assertTrue(isset($config->hostname)); // top level
            $this->assertTrue(isset($config->db->name)); // one level down
        }
    }

    public function testModification()
    {
        $config = new Zend_Config($this->_all, true);

        // overwrite an existing key
        $this->assertEquals('thisname', $config->name);
        $config->name = 'anothername';
        $this->assertEquals('anothername', $config->name);

        // overwrite an existing multi-level key
        $this->assertEquals('multi', $config->one->two->three);
        $config->one->two->three = 'anothername';
        $this->assertEquals('anothername', $config->one->two->three);

        // create a new multi-level key
        $config->does = array('not'=> array('exist' => 'yet'));
        $this->assertEquals('yet', $config->does->not->exist);

    }

    public function testNoModifications()
    {
        $config = new Zend_Config($this->_all);
        try {
            $config->hostname = 'test';
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('is read only', $expected->getMessage());
            return;
        }
        $this->fail('An expected Zend_Config_Exception has not been raised');
    }

    public function testNoNestedModifications()
    {
        $config = new Zend_Config($this->_all);
        try {
            $config->db->host = 'test';
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('is read only', $expected->getMessage());
            return;
        }
        $this->fail('An expected Zend_Config_Exception has not been raised');
    }

    public function testNumericKeys()
    {
        $data = new Zend_Config($this->_numericData);
        $this->assertEquals('test', $data->{1});
        $this->assertEquals(34, $data->{0});
    }

    public function testCount()
    {
        $data = new Zend_Config($this->_menuData1);
        $this->assertEquals(3, count($data->button));
    }

    public function testIterator()
    {
        // top level
        $config = new Zend_Config($this->_all);
        $var = '';
        foreach ($config as $key=>$value) {
            if (is_string($value)) {
                $var .= "\nkey = $key, value = $value";
            }
        }
        $this->assertContains('key = name, value = thisname', $var);

        // 1 nest
        $var = '';
        foreach ($config->db as $key=>$value) {
            $var .= "\nkey = $key, value = $value";
        }
        $this->assertContains('key = host, value = 127.0.0.1', $var);

        // 2 nests
        $config = new Zend_Config($this->_menuData1);
        $var = '';
        foreach ($config->button->b1 as $key=>$value) {
            $var .= "\nkey = $key, value = $value";
        }
        $this->assertContains('key = L1, value = button1-1', $var);
    }

    public function testArray()
    {
        $config = new Zend_Config($this->_all);

        ob_start();
        print_r($config->toArray());
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertContains('Array', $contents);
        $this->assertContains('[hostname] => all', $contents);
        $this->assertContains('[user] => username', $contents);
    }

    public function testErrorWriteToReadOnly()
    {
        $config = new Zend_Config($this->_all);
        try {
            $config->test = '32';
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('read only', $expected->getMessage());
            return;
        }

        $this->fail('An expected Zend_Config_Exception has not been raised');
    }

    public function testZF343()
    {
        $config_array = array(
            'controls' => array(
                'visible' => array(
                    'name' => 'visible',
                    'type' => 'checkbox',
                    'attribs' => array(), // empty array
                ),
            ),
        );
        $form_config = new Zend_Config($config_array, true);
        $this->assertSame(array(), $form_config->controls->visible->attribs->toArray());
    }

    public function testZF402()
    {
        $configArray = array(
            'data1'  => 'someValue',
            'data2'  => 'someValue',
            'false1' => false,
            'data3'  => 'someValue'
            );
        $config = new Zend_Config($configArray);
        $this->assertTrue(count($config) === count($configArray));
        $count = 0;
        foreach ($config as $key => $value) {
            if ($key === 'false1') {
                $this->assertTrue($value === false);
            } else {
                $this->assertTrue($value === 'someValue');
            }
            $count++;
        }
        $this->assertTrue($count === 4);
    }
    
    public function testZf1019_HandlingInvalidKeyNames()
    {
        $config = new Zend_Config($this->_leadingdot);
        $array = $config->toArray();
        $this->assertContains('dot-test', $array['.test']);
    }

    public function testZF1019_EmptyKeys()
    {
        $config = new Zend_Config($this->_invalidkey);
        $array = $config->toArray();
        $this->assertContains('test', $array[' ']);
        $this->assertContains('test', $array['']);
    }
    
    public function testZF1417_DefaultValues()
    {
        $config = new Zend_Config($this->_all);
        $value = $config->get('notthere', 'default');
        $this->assertTrue($value === 'default');
        $this->assertTrue($config->notThere === null);

    }
    
}

