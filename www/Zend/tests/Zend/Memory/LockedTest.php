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
class Zend_Memory_Container_LockedTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        $this->assertTrue($memObject instanceof Zend_Memory_Container_Locked);
    }


    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        // value property
        $this->assertEquals((string)$memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string)$memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertEquals((string)$memObject->value, 'another value');
    }


    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        // It's always locked
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->unlock();
        // It's always locked
        $this->assertTrue((boolean)$memObject->isLocked());
    }

    /**
     * tests the touch() method
     */
    public function testTouch()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        $memObject->touch();

        // Nothing to check
    }
}
