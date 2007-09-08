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
class Zend_Memory_MemoryManagerTest extends PHPUnit_Framework_TestCase
{

    /**
     * tests the Memory Manager creation
     *
     */
    public function testCreation()
    {
        /** 'File' backend */
        $backendOptions = array('cacheDir' => dirname(__FILE__) . '/_files/'); // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
    }


    /**
     * tests the Memory Manager settings
     */
    public function testSettings()
    {
        /** 'File' backend */
        $backendOptions = array('cacheDir' => dirname(__FILE__) . '/_files/'); // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);

        // MemoryLimit
        $memoryManager->setMemoryLimit(2*1024*1024 /* 2Mb */);
        $this->assertEquals($memoryManager->getMemoryLimit(), 2*1024*1024);

        // MinSize
        $this->assertEquals($memoryManager->getMinSize(), 16*1024); // check for default value (16K)
        $memoryManager->setMinSize(4*1024 /* 4Kb */);
        $this->assertEquals($memoryManager->getMinSize(), 4*1024);
    }


    /**
     * tests the memory Objects creation
     */
    public function testCreate()
    {
        /** 'File' backend */
        $backendOptions = array('cacheDir' => dirname(__FILE__) . '/_files/'); // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);

        $memObject1 = $memoryManager->create('Value of object 1');
        $this->assertTrue($memObject1 instanceof Zend_Memory_AccessController);
        $this->assertEquals($memObject1->getRef(), 'Value of object 1');

        $memObject2 = $memoryManager->create();
        $this->assertTrue($memObject2 instanceof Zend_Memory_AccessController);
        $this->assertEquals($memObject2->getRef(), '');

        $memObject3 = $memoryManager->createLocked('Value of object 3');
        $this->assertTrue($memObject3 instanceof Zend_Memory_Container_Locked);
        $this->assertEquals($memObject3->getRef(), 'Value of object 3');

        $memObject4 = $memoryManager->createLocked();
        $this->assertTrue($memObject4 instanceof Zend_Memory_Container_Locked);
        $this->assertEquals($memObject4->getRef(), '');
    }


    /**
     * tests the processing of data
     */
    public function testProcessing()
    {
        /** 'File' backend */
        $backendOptions = array('cacheDir' => dirname(__FILE__) . '/_files/'); // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);

        $memoryManager->setMinSize(256);
        $memoryManager->setMemoryLimit(1024*32);

        $memObjects = array();
        for ($count = 0; $count < 64; $count++) {
            $memObject = $memoryManager->create(str_repeat((string)($count % 10), 1024) /* 1K */);
            $memObjects[] = $memObject;
        }

        for ($count = 0; $count < 64; $count += 2) {
            if (version_compare(PHP_VERSION, '5.2') < 0) {
                $value = $memObjects[$count]->getRef();
                $this->assertEquals($value[16], (string)($count % 10));
            } else {
                $this->assertEquals($memObjects[$count]->value[16], (string)($count % 10));
            }
        }

        for ($count = 63; $count > 0; $count -= 2) {
            if (version_compare(PHP_VERSION, '5.2') < 0) {
                $value = &$memObjects[$count]->getRef();
                $value[16] = '_';
            } else {
                $memObjects[$count]->value[16] = '_';
            }
        }

        for ($count = 1; $count < 64; $count += 2) {
            if (version_compare(PHP_VERSION, '5.2') < 0) {
                $value = $memObjects[$count]->getRef();
                $this->assertEquals($value[16], '_');
            } else {
                $this->assertEquals($memObjects[$count]->value[16], '_');
            }
        }
    }
}
