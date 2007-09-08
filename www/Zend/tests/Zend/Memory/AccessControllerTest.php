<?php
/**
 * @package    Zend_Memory
 * @subpackage UnitTests
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite

/** Zend_Memory */
require_once 'Zend/Memory.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';


/**
 * @package    Zend_Memory
 * @subpackage UnitTests
 */
class Zend_Memory_Container_AccessControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Memory manager, used for tests
     *
     * @var Zend_Memory_Manager
     */
    private $_memoryManager = null;

    /**
     * Retrieve memory manager
     *
     */
    private function _getMemoryManager()
    {
        if ($this->_memoryManager === null) {
            $backendOptions = array('cacheDir' => dirname(__FILE__) . '/_files/'); // Directory where to put the cache files
            $this->_memoryManager = Zend_Memory::factory('File', $backendOptions);
        }

        return $this->_memoryManager;
    }



    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager  = $this->_getMemoryManager();
        $memObject      = $memoryManager->create('012345678');

        $this->assertTrue($memObject instanceof Zend_Memory_AccessController);
    }


    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager  = $this->_getMemoryManager();
        $memObject      = $memoryManager->create('0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip next tests for PHP versions before 5.2
            return;
        }

        // value property
        $this->assertEquals((string)$memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string)$memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertTrue($memObject->value instanceof Zend_Memory_Value);
        $this->assertEquals((string)$memObject->value, 'another value');
    }


    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memoryManager  = $this->_getMemoryManager();
        $memObject      = $memoryManager->create('012345678');

        $this->assertFalse((boolean)$memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse((boolean)$memObject->isLocked());
    }
}
