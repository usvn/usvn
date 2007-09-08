<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AccelerationTest.php 3574 2007-02-22 22:01:50Z thomas $
 */

/**
 * Zend_Measure_Acceleration
 */
require_once 'Zend/Measure/Acceleration.php';
require_once 'Zend/Locale.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_AccelerationTest extends PHPUnit_Framework_TestCase
{

    /**
     * test for new object
     * expected instance
     */
    public function testAccelerationInit()
    {
        $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Acceleration,'Zend_Measure_Acceleration Object not returned');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');
        // no type
        $value = new Zend_Measure_Acceleration('100','de');
        $this->assertTrue($value instanceof Zend_Measure_Acceleration,'Zend_Measure_Acceleration Object not returned');
        // unknown type
        try {
            $value = new Zend_Measure_Acceleration('100','Acceleration::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
        // no value
        try {
            $value = new Zend_Measure_Acceleration('novalue',Zend_Measure_Acceleration::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
        // false locale
        try {
            $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
        // no locale
        $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Acceleration value expected');

        // negative value
        $locale = new Zend_Locale('de');
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,$locale);
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a negative integer');
        // seperated value
        $value = new Zend_Measure_Acceleration('-100,200',Zend_Measure_Acceleration::STANDARD,$locale);
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a decimal value');
        // negative seperated value
        $value = new Zend_Measure_Acceleration('-100.100,200',Zend_Measure_Acceleration::STANDARD,$locale);
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
        // value with string
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
    }


    /**
     * test for equals()
     * expected true
     */
    public function testAccelerationEquals()
    {
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $newvalue = new Zend_Measure_Acceleration('otherstring -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Acceleration Object should be equal');

        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $newvalue = new Zend_Measure_Acceleration('otherstring -100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Acceleration Object should be not equal');
    }


    /**
     * test for setvalue()
     * expected integer
     */
    public function testAccelerationSetValue()
    {
        $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');

        $locale = new Zend_Locale('de_AT');
        $value->setValue('200',$locale);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');
        $value->setValue('200','de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');
        $value->setValue('-200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a negative integer');
        $value->setValue('-200,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a decimal value');
        $value->setValue('-200.200,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
        $value->setValue('200', Zend_Measure_Acceleration::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');

        try {
            $value->setValue('otherstring -200.200,200','Acceleration::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }

        try {
            $value->setValue('novalue',Zend_Measure_Acceleration::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }

        try {
            $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Acceleration::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test setting type
     * expected new type
     */
    public function testAccelerationSetType()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setType(Zend_Measure_Acceleration::GRAV);
        $this->assertEquals($value->getType(), Zend_Measure_Acceleration::GRAV, 'Zend_Measure_Acceleration type expected');

        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::MILE_PER_HOUR_MINUTE,'de');
        $value->setType(Zend_Measure_Acceleration::GRAV);
        $this->assertEquals($value->getType(), Zend_Measure_Acceleration::GRAV, 'Zend_Measure_Acceleration type expected');

        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::GRAV,'de');
        $value->setType(Zend_Measure_Acceleration::MILE_PER_HOUR_MINUTE);
        $this->assertEquals($value->getType(), Zend_Measure_Acceleration::MILE_PER_HOUR_MINUTE, 'Zend_Measure_Acceleration type expected');

        try {
            $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
            $value->setType('Acceleration::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testAccelerationToString()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals($value->toString(), '-100 m/s²', 'Value -100 m/s² expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testAcceleration_ToString()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals($value->__toString(), '-100 m/s²', 'Value -100 m/s² expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testAccelerationConversionList()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }


    /**
     * test convertTo
     * expected array
     */
    public function testAccelerationConvertTo()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $unit  = $value->convertTo(Zend_Measure_Acceleration::GRAV);
        $this->assertEquals($value->getType(), Zend_Measure_Acceleration::GRAV, 'Zend_Measure_Acceleration type expected');
    }


    /**
     * test add
     * expected array
     */
    public function testAccelerationAdd()
    {
        $value  = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $value2 = new Zend_Measure_Acceleration('200',Zend_Measure_Acceleration::STANDARD,'de');
        $value->add($value2);
        $this->assertEquals($value->getValue(), 100, 'value 100 expected');
    }


    /**
     * test add
     * expected array
     */
    public function testAccelerationSub()
    {
        $value  = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $value2 = new Zend_Measure_Acceleration('200',Zend_Measure_Acceleration::STANDARD,'de');
        $value->sub($value2);
        $this->assertEquals($value->getValue(), -300, 'value -300 expected');
    }


    /**
     * test add
     * expected array
     */
    public function testAccelerationCompare()
    {
        $value  = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $value2 = new Zend_Measure_Acceleration('200',Zend_Measure_Acceleration::STANDARD,'de');
        $value3 = new Zend_Measure_Acceleration('200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals($value->compare($value2), -1);
        $this->assertEquals($value2->compare($value), 1);
        $this->assertEquals($value2->compare($value3), 0);
    }
}
