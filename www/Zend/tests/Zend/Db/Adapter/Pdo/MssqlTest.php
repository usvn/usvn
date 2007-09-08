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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: MssqlTest.php 5906 2007-07-28 02:58:20Z bkarwin $
 */

/**
 * @see Zend_Db_Adapter_Pdo_TestCommon
 */
require_once 'Zend/Db/Adapter/Pdo/TestCommon.php';

/**
 * @see Zend_Db_Adapter_Pdo_Mysql
 */
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_MssqlTest extends Zend_Db_Adapter_Pdo_TestCommon
{

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INT'                => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'TINYINT'            => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE,
        'MONEY'              => Zend_Db::FLOAT_TYPE,
        'NUMERIC'            => Zend_Db::FLOAT_TYPE,
        'REAL'               => Zend_Db::FLOAT_TYPE,
        'SMALLMONEY'         => Zend_Db::FLOAT_TYPE
    );

    public function testAdapterDescribeTableAttributeColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts',        $desc['product_name']['TABLE_NAME']);
        $this->assertEquals('product_name',      $desc['product_name']['COLUMN_NAME']);
        $this->assertEquals(2,                   $desc['product_name']['COLUMN_POSITION']);
        $this->assertRegExp('/varchar/i',        $desc['product_name']['DATA_TYPE']);
        $this->assertEquals('',                  $desc['product_name']['DEFAULT']);
        $this->assertFalse(                      $desc['product_name']['NULLABLE'], 'Expected product_name not to be nullable');
        $this->assertEquals(0,                   $desc['product_name']['SCALE'], 'scale is not 0');
        // MS SQL Server reports varchar length in the PRECISION field.  Whaaa?!?
        $this->assertEquals(100,                 $desc['product_name']['PRECISION'], 'precision is not 100');
        $this->assertFalse(                      $desc['product_name']['PRIMARY'], 'Expected product_name not to be a primary key');
        $this->assertNull(                       $desc['product_name']['PRIMARY_POSITION'], 'Expected product_name to return null for PRIMARY_POSITION');
        $this->assertFalse(                      $desc['product_name']['IDENTITY'], 'Expected product_name to return false for IDENTITY');
    }

    public function testAdapterDescribeTablePrimaryKeyColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts',        $desc['product_id']['TABLE_NAME']);
        $this->assertEquals('product_id',        $desc['product_id']['COLUMN_NAME']);
        $this->assertEquals(1,                   $desc['product_id']['COLUMN_POSITION']);
        $this->assertEquals('',                  $desc['product_id']['DEFAULT']);
        $this->assertFalse(                      $desc['product_id']['NULLABLE'], 'Expected product_id not to be nullable');
        $this->assertEquals(0,                   $desc['product_id']['SCALE'], 'scale is not 0');
        $this->assertEquals(10,                  $desc['product_id']['PRECISION'], 'precision is not 10');
        $this->assertTrue(                       $desc['product_id']['PRIMARY'], 'Expected product_id to be a primary key');
        $this->assertEquals(1,                   $desc['product_id']['PRIMARY_POSITION']);
    }

    public function testAdapterInsertSequence()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support sequences.');
    }

    public function getDriver()
    {
        return 'Pdo_Mssql';
    }

}
