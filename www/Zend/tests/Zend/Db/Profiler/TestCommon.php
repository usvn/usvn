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
 * @see Zend_Db_TestSetup
 */
require_once 'Zend/Db/TestSetup.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Profiler_TestCommon extends Zend_Db_TestSetup
{

    public function setUp()
    {
        parent::setUp();
        $this->_db->getProfiler()->setEnabled(true);
    }

    public function tearDown()
    {
        $this->_db->getProfiler()->setEnabled(false);
        parent::tearDown();
    }

    public function testProfilerPreparedStatement()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);

        // prepare a query
        $select = $this->_db->select()
            ->from('zfbugs')
            ->where("$bug_id = 2");
        $stmt = $this->_db->prepare($select->__toString());

        // execute query a first time
        $stmt->execute();
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(2, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->_db->getProfiler()->getQueryProfiles();
        $this->assertType('array', $profiles);
        $this->assertEquals(1, count($profiles), 'Expected to find 1 profile');
        $qp = $profiles[0];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // execute query a second time
        $stmt->execute();
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(2, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->_db->getProfiler()->getQueryProfiles();
        $this->assertType('array', $profiles);
        $this->assertEquals(2, count($profiles), 'Expected to find 2 profiles');
        $qp = $profiles[1];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        $this->assertNotSame($profiles[0], $profiles[1]);
        $this->assertEquals($profiles[0]->getQuery(), $profiles[1]->getQuery());
    }

    public function testProfilerPreparedStatementWithParams()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);

        // prepare a query
        $select = $this->_db->select()
            ->from('zfbugs')
            ->where("$bug_id = ?");
        $stmt = $this->_db->prepare($select->__toString());

        // execute query a first time
        $stmt->execute(array(2));
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(2, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->_db->getProfiler()->getQueryProfiles();
        $this->assertType('array', $profiles);
        $this->assertEquals(1, count($profiles), 'Expected to find 1 profile');
        $qp = $profiles[0];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = ?", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(1 => 2), $params);

        // execute query a second time
        $stmt->execute(array(3));
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(3, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->_db->getProfiler()->getQueryProfiles();
        $this->assertType('array', $profiles);
        $this->assertEquals(2, count($profiles), 'Expected to find 2 profiles');
        $qp = $profiles[1];
        $this->assertType('Zend_Db_Profiler_Query', $qp);
        $this->assertNotSame($profiles[0], $profiles[1]);
        $this->assertEquals($profiles[0]->getQuery(), $profiles[1]->getQuery());

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = ?", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(1 => 3), $params);

        $this->assertNotSame($profiles[0], $profiles[1]);
        $this->assertEquals($profiles[0]->getQuery(), $profiles[1]->getQuery());
    }

    public function testProfilerPreparedStatementWithBoundParams()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);

        // prepare a query
        $select = $this->_db->select()
            ->from('zfbugs')
            ->where("$bug_id = ?");
        $stmt = $this->_db->prepare($select->__toString());

        // execute query a first time
        $id = 1;
        $this->assertTrue($stmt->bindParam(1, $id));
        $id = 2;
        $stmt->execute();
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(2, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->_db->getProfiler()->getQueryProfiles();
        $this->assertType('array', $profiles, 'Expected array, got '.gettype($profiles));
        $this->assertEquals(1, count($profiles), 'Expected to find 1 profile');
        $qp = $profiles[0];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = ?", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(1 => 2), $params);

        // execute query a second time
        $id = 3;
        $stmt->execute();
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(3, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->_db->getProfiler()->getQueryProfiles();
        $this->assertType('array', $profiles, 'Expected array, got '.gettype($profiles));
        $this->assertEquals(2, count($profiles), 'Expected to find 2 profiles');
        $qp = $profiles[1];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = ?", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(1 => 3), $params);
    }

}
