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
 * @version    $Id: StaticTest.php 4700 2007-05-04 04:25:11Z bkarwin $
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * PHPUnit_Util_Filter
 */
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Zend_Db_Adapter_Static
 */
require_once 'Zend/Db/Adapter/Static.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_StaticTest extends PHPUnit_Framework_TestCase
{

    public function testDbFactory()
    {
        $db = Zend_Db::factory('Static', array('dbname' => 'dummy') );
        $this->assertType('Zend_Db_Adapter_Abstract', $db);
    }

    public function testDbConstructorWithoutFactory()
    {
        $db = new Zend_Db_Adapter_Static( array('dbname' => 'dummy') );
        $this->assertType('Zend_Db_Adapter_Abstract', $db);
    }

    public function testDbGetConnection()
    {
        $db = Zend_Db::factory('Static', array('dbname' => 'dummy'));

        $conn = $db->getConnection();
        $this->assertType('Zend_Db_Adapter_Static', $conn);
    }

    public function testDbGetFetchMode()
    {
        $db = Zend_Db::factory('Static', array('dbname' => 'dummy'));
        $mode = $db->getFetchMode();
        $this->assertType('integer', $mode);
    }

    public function testDbExceptionInvalidDriverName()
    {
        try {
            $db = Zend_Db::factory(null);
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Exception', $e,
                'Expected exception of type Zend_Db_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Adapter name must be specified in a string.');
        }
    }

    public function testDbExceptionInvalidOptionsArray()
    {
        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                $db = Zend_Db::factory('Static', 'scalar');
                $this->fail('Expected exception not thrown');
            } catch (Exception $e) {
                $this->assertContains('Argument 2 passed to Zend_Db::factory() must be an array, string given',
                                      $e->getMessage());
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }
    }

    public function testDbExceptionInvalidOptionsArrayWithoutFactory()
    {
        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                $db = new Zend_Db_Adapter_Static('scalar');
                $this->fail('Expected exception not thrown');
            } catch (Exception $e) {
                $this->assertContains('Argument 1 passed to Zend_Db_Adapter_Abstract::__construct() must be an array, '
                                    . 'string given', $e->getMessage());
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }
    }

    public function testDbExceptionNoConfig()
    {
        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                $db = Zend_Db::factory('Static');
                $this->fail('Expected exception not thrown');
            } catch (Exception $e) {
                $this->assertContains('Configuration must have a key for \'dbname\' that names the database instance.',
                                      $e->getMessage());
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }
    }

    public function testDbExceptionNoDatabaseName()
    {
        try {
            $db = Zend_Db::factory('Static', array());
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expected exception of type Zend_Db_Adapter_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), "Configuration must have a key for 'dbname' that names the database instance.");
        }
    }

    public function getDriver()
    {
        return 'Static';
    }

}
