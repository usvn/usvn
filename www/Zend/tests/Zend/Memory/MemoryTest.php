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
class Zend_Memory_MemoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * tests the Memory Manager creation
     *
     */
    public function testCreation()
    {
        /** 'None' backend */
        $memoryManager = Zend_Memory::factory('None');
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
        unset($memoryManager);

        /** 'File' backend */
        $backendOptions = array('cacheDir' => dirname(__FILE__) . '/_files/'); // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
        unset($memoryManager);
    }
}

