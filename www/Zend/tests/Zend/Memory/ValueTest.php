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
 * Memory value container
 *
 * (Should be presented for value object)
 */
class Zend_Memory_Container_Movable_Dummy extends Zend_Memory_Container_Movable
{
    /**
     * Dummy object constructor
     */
    public function __construct()
    {
        // Do nothing
    }

    /**
     * Dummy value update callback method
     */
    public function processUpdate()
    {
        // Do nothing
    }
}


/**
 * @package    Zend_Memory
 * @subpackage UnitTests
 */
class Zend_Memory_ValueTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests the Value object creation
     */
    public function testCreation()
    {
        $valueObject = new Zend_Memory_Value('data data data ...', new Zend_Memory_Container_Movable_Dummy());
        $this->assertTrue($valueObject instanceof Zend_Memory_Value);
        $this->assertEquals($valueObject->getRef(), 'data data data ...');
    }


    /**
     * tests the value reference retrieval
     */
    public function testGetRef()
    {
        $valueObject = new Zend_Memory_Value('0123456789', new Zend_Memory_Container_Movable_Dummy());
        $valueRef = &$valueObject->getRef();
        $valueRef[3] = '_';

        $this->assertEquals($valueObject->getRef(), '012_456789');
    }


    /**
     * tests the __toString() functionality
     */
    public function testToString()
    {
        $valueObject = new Zend_Memory_Value('0123456789', new Zend_Memory_Container_Movable_Dummy());
        $this->assertEquals($valueObject->__toString(), '0123456789');

        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip following tests for PHP versions before 5.2
            return;
        }

        $this->assertEquals(strlen($valueObject), 10);
        $this->assertEquals((string)$valueObject, '0123456789');
    }

    /**
     * tests the access through ArrayAccess methods
     */
    public function testArrayAccess()
    {
        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip following tests for PHP versions before 5.2
            return;
        }

        $valueObject = new Zend_Memory_Value('0123456789', new Zend_Memory_Container_Movable_Dummy());
        $this->assertEquals($valueObject[8], '8');

        $valueObject[2] = '_';
        $this->assertEquals((string)$valueObject, '01_3456789');

        $valueObject[10] = '_';
        $this->assertEquals((string)$valueObject, '01_3456789_');
    }
}
