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
class Zend_Db_Profiler_StaticTest extends Zend_Db_TestSetup
{
    public function getDriver()
    {
        return 'Static';
    }

    /**
     * Ensures that Zend_Db::factory() properly controls whether the adapter's profiler is enabled.
     *
     * @return void
     */
    public function testProfilerFactory()
    {
        $db = Zend_Db::factory('Static',
            array(
                'dbname'   => 'dummy',
                'profiler' => false
            )
        );
        $this->assertType('Zend_Db_Adapter_Abstract', $db,
            'Expected object of type Zend_Db_Adapter_Abstract, got '.get_class($db));

        $prof = $db->getProfiler();
        $this->assertType('Zend_Db_Profiler', $prof,
            'Expected object of type Zend_Db_Profiler, got '.get_class($prof));
        $this->assertFalse($prof->getEnabled());

        $db = Zend_Db::factory('Static',
            array(
                'dbname' => 'dummy',
                'profiler' => true
            )
        );
        $this->assertType('Zend_Db_Adapter_Abstract', $db,
            'Expected object of type Zend_Db_Adapter_Abstract, got '.get_class($db));

        $prof = $db->getProfiler();
        $this->assertType('Zend_Db_Profiler', $prof,
            'Expected object of type Zend_Db_Profiler, got '.get_class($prof));
        $this->assertTrue($prof->getEnabled());
    }

    /**
     * Ensures that setEnabled() behaves as expected.
     *
     * @return void
     */
    public function testProfilerSetEnabled()
    {
        $prof = $this->_db->getProfiler();

        $this->assertSame($prof->setEnabled(true), $prof);
        $this->assertTrue($prof->getEnabled());

        $this->assertSame($prof->setEnabled(false), $prof);
        $this->assertFalse($prof->getEnabled());
    }

    /**
     * Ensures that setFilterElapsedSecs() behaves as expected.
     *
     * @return void
     */
    public function testProfilerSetFilterElapsedSecs()
    {
        $prof = $this->_db->getProfiler();

        $this->assertSame($prof->setFilterElapsedSecs(), $prof);
        $this->assertNull($prof->getFilterElapsedSecs());

        $this->assertSame($prof->setFilterElapsedSecs(null), $prof);
        $this->assertNull($prof->getFilterElapsedSecs());

        $this->assertSame($prof->setFilterElapsedSecs(3), $prof);
        $this->assertEquals(3, $prof->getFilterElapsedSecs());
    }

    /**
     * Ensures that setFilterQueryType() behaves as expected.
     *
     * @return void
     */
    public function testProfilerSetFilterQueryType()
    {
        $prof = $this->_db->getProfiler();

        $this->assertSame($prof->setFilterQueryType(), $prof);
        $this->assertNull($prof->getFilterQueryType());

        $this->assertSame($prof->setFilterQueryType(null), $prof);
        $this->assertNull($prof->getFilterQueryType());

        $queryTypes = Zend_Db_Profiler::DELETE;
        $this->assertSame($prof->setFilterQueryType($queryTypes), $prof);
        $this->assertEquals($queryTypes, $prof->getFilterQueryType());
    }

    /**
     * Ensures that clear() behaves as expected
     *
     * @return void
     */
    public function testProfilerClear()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $this->assertSame($prof->clear(), $prof);

        $this->assertFalse($prof->getQueryProfiles());
    }

    /**
     * Ensures that queryStart() behaves as expected.
     *
     * @return void
     */
    public function testProfilerQueryStart()
    {
        $prof = $this->_db->getProfiler();

        $this->assertNull($prof->queryStart('sqlDisabled1'));

        $prof->setEnabled(true);

        $queries = array(
            array(
                'sql'          => '',
                'typeGiven'    => null,
                'typeExpected' => Zend_Db_Profiler::QUERY
                ),
            array(
                'sql'          => '',
                'typeGiven'    => Zend_Db_Profiler::QUERY,
                'typeExpected' => Zend_Db_Profiler::QUERY
                ),
            array(
                'sql'          => 'something',
                'typeGiven'    => null,
                'typeExpected' => Zend_Db_Profiler::QUERY
                ),
            array(
                'sql'          => 'INSERT',
                'typeGiven'    => null,
                'typeExpected' => Zend_Db_Profiler::INSERT
                ),
            array(
                'sql'          => 'sqlInsert',
                'typeGiven'    => Zend_Db_Profiler::INSERT,
                'typeExpected' => Zend_Db_Profiler::INSERT
                ),
            array(
                'sql'          => 'INSERT',
                'typeGiven'    => Zend_Db_Profiler::UPDATE,
                'typeExpected' => Zend_Db_Profiler::UPDATE
                ),
            array(
                'sql'          => 'UPDATE',
                'typeGiven'    => null,
                'typeExpected' => Zend_Db_Profiler::UPDATE
                ),
            array(
                'sql'          => 'sqlUpdate',
                'typeGiven'    => Zend_Db_Profiler::UPDATE,
                'typeExpected' => Zend_Db_Profiler::UPDATE
                ),
            array(
                'sql'          => 'UPDATE',
                'typeGiven'    => Zend_Db_Profiler::DELETE,
                'typeExpected' => Zend_Db_Profiler::DELETE
                ),
            array(
                'sql'          => 'DELETE',
                'typeGiven'    => null,
                'typeExpected' => Zend_Db_Profiler::DELETE
                ),
            array(
                'sql'          => 'sqlDelete',
                'typeGiven'    => Zend_Db_Profiler::DELETE,
                'typeExpected' => Zend_Db_Profiler::DELETE
                ),
            array(
                'sql'          => 'DELETE',
                'typeGiven'    => Zend_Db_Profiler::SELECT,
                'typeExpected' => Zend_Db_Profiler::SELECT
                ),
            array(
                'sql'          => 'SELECT',
                'typeGiven'    => null,
                'typeExpected' => Zend_Db_Profiler::SELECT
                ),
            array(
                'sql'          => 'sqlSelect',
                'typeGiven'    => Zend_Db_Profiler::SELECT,
                'typeExpected' => Zend_Db_Profiler::SELECT
                ),
            array(
                'sql'          => 'SELECT',
                'typeGiven'    => Zend_Db_Profiler::INSERT,
                'typeExpected' => Zend_Db_Profiler::INSERT
                )
            );

        foreach ($queries as $key => $query) {
            $this->assertEquals($key, $prof->queryStart($query['sql'], $query['typeGiven']));
        }

        $prof->setEnabled(false);

        $this->assertNull($prof->queryStart('sqlDisabled2'));

        $queryProfiles = $prof->getQueryProfiles(null, true);

        $this->assertEquals(count($queries), count($queryProfiles));

        foreach ($queryProfiles as $queryId => $queryProfile) {
            $this->assertTrue(isset($queries[$queryId]));
            $this->assertEquals($queries[$queryId]['sql'], $queryProfile->getQuery());
            $this->assertEquals($queries[$queryId]['typeExpected'], $queryProfile->getQueryType());
        }
    }

    /**
     * Ensures that queryEnd() behaves as expected when given invalid query handle.
     *
     * @return void
     */
    public function testProfilerQueryEndHandleInvalid()
    {
        $prof = $this->_db->getProfiler();

        $prof->queryEnd('invalid');

        $prof->setEnabled(true);

        try {
            $prof->queryEnd('invalid');
            $this->fail('Expected Zend_Db_Profiler_Exception not thrown');
        } catch (Zend_Db_Profiler_Exception $e) {
            $this->assertContains('no query with handle', $e->getMessage());
        }
    }

    /**
     * Ensures that queryEnd() throws an exception when the query has already ended.
     *
     * @return void
     */
    public function testProfilerQueryEndAlreadyEnded()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $queryId = $prof->queryStart('sql');

        $prof->queryEnd($queryId);

        try {
            $prof->queryEnd($queryId);
            $this->fail('Expected Zend_Db_Profiler_Exception not thrown');
        } catch (Zend_Db_Profiler_Exception $e) {
            $this->assertContains('has already ended', $e->getMessage());
        }
    }

    /**
     * Ensures that queryEnd() does not keep the query profile if the elapsed time is less than
     * the minimum time allowed by the elapsed seconds filter.
     *
     * @return void
     */
    public function testProfilerQueryEndFilterElapsedSecs()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true)
                ->setFilterElapsedSecs(1);

        $prof->queryEnd($prof->queryStart('sqlTimeShort1'));

        $this->_db->query('sqlTimeShort2');

        $this->_db->setOnQuerySleep(2);

        $this->_db->query('sqlTimeLong');

        $this->_db->setOnQuerySleep(0);

        $this->_db->query('sqlTimeShort3');

        $this->assertEquals(1, count($queryProfiles = $prof->getQueryProfiles()));

        $this->assertTrue(isset($queryProfiles[2]));

        $this->assertEquals('sqlTimeLong', $queryProfiles[2]->getQuery());
    }

    /**
     * Ensures that queryEnd() does not keep the query profile if the query type is not allowed
     * by the query type filter.
     *
     * @return void
     */
    public function testProfilerQueryEndFilterQueryType()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true)
                ->setFilterQueryType(Zend_Db_Profiler::UPDATE);

        $this->_db->query('INSERT');
        $this->_db->query('UPDATE');
        $this->_db->query('DELETE');
        $this->_db->query('SELECT');
        $this->_db->query('UPDATE');
        $this->_db->query('DELETE');

        $this->assertEquals(2, count($prof->getQueryProfiles()));
    }

    /**
     * Ensures that getQueryProfile() throws an exception when given an invalid query handle.
     *
     * @return void
     */
    public function testProfilerGetQueryProfileHandleInvalid()
    {
        try {
            $this->_db->getProfiler()->getQueryProfile('invalid');
            $this->fail('Expected Zend_Db_Profiler_Exception not thrown');
        } catch (Zend_Db_Profiler_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    /**
     * Ensures that getQueryProfile() behaves as expected when provided a valid handle.
     *
     * @return void
     */
    public function testProfilerGetQueryProfileHandleValid()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $queryId = $prof->queryStart('sql');

        $this->assertType('Zend_Db_Profiler_Query',
            $prof->getQueryProfile($queryId));
    }

    /**
     * Ensures that getQueryProfiles() returns only those queries of the specified type(s).
     *
     * @return void
     */
    public function testProfilerGetQueryProfilesFilterQueryType()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $queries = array(
            'INSERT',
            'UPDATE',
            'DELETE',
            'SELECT'
            );

        foreach ($queries as $query) {
            $this->_db->query($query);
        }

        foreach ($queries as $queryId => $query) {
            $queryProfiles = $prof->getQueryProfiles(constant("Zend_Db_Profiler::$query"));
            $this->assertEquals(1, count($queryProfiles));
            $this->assertTrue(isset($queryProfiles[$queryId]));
            $this->assertEquals($query, $queryProfiles[$queryId]->getQuery());
        }
    }

    /**
     * Ensures that getTotalElapsedSecs() behaves as expected for basic usage.
     *
     * @return void
     */
    public function testProfilerGetTotalElapsedSecsBasic()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $this->_db->setOnQuerySleep(1);

        $numQueries = 3;

        for ($queryCount = 0; $queryCount < $numQueries; ++$queryCount) {
            $this->_db->query("sql $queryCount");
        }

        $this->assertThat(
            $prof->getTotalElapsedSecs(),
            $this->greaterThan($numQueries - 0.1)
            );
    }

    /**
     * Ensures that getTotalElapsedSecs() only counts queries of the specified type(s).
     *
     * @return void
     */
    public function testProfilerGetTotalElapsedSecsFilterQueryType()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $this->_db->query('SELECT');

        $this->_db->query('SELECT');

        $this->_db->setOnQuerySleep(1);

        $this->_db->query('UPDATE');

        $this->_db->setOnQuerySleep(0);

        $this->_db->query('SELECT');

        $this->_db->setOnQuerySleep(1);

        $this->_db->query('UPDATE');

        $this->_db->setOnQuerySleep(0);

        $this->_db->query('SELECT');

        $this->assertThat(
            $prof->getTotalElapsedSecs(Zend_Db_Profiler::SELECT),
            $this->lessThan(1)
            );

        $this->assertThat(
            $prof->getTotalElapsedSecs(Zend_Db_Profiler::UPDATE),
            $this->greaterThan(1.9)
            );
    }

    /**
     * Ensures that getTotalNumQueries() behaves as expected for basic usage.
     *
     * @return void
     */
    public function testProfilerGetTotalNumQueries()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $numQueries = 3;

        for ($queryCount = 0; $queryCount < $numQueries; ++$queryCount) {
            $this->_db->query("sql $queryCount");
        }

        $this->assertEquals($numQueries, $prof->getTotalNumQueries());
    }

    /**
     * Ensures that getTotalNumQueries() only counts queries of the specified type(s).
     *
     * @return void
     */
    public function testProfilerGetTotalNumQueriesFilterQueryType()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $queries = array(
            'INSERT' => 2,
            'UPDATE' => 5,
            'DELETE' => 1,
            'SELECT' => 3
            );

        foreach ($queries as $querySql => $queryCount) {
            for ($i = 0; $i < $queryCount; ++$i) {
                $this->_db->query("$querySql $i");
            }
        }

        foreach ($queries as $querySql => $queryCount) {
            $this->assertEquals($queryCount, $prof->getTotalNumQueries(constant("Zend_Db_Profiler::$querySql")));
        }
    }

    /**
     * Ensures that getLastQueryProfile() returns false when no queries have been profiled.
     *
     * @return void
     */
    public function testProfilerGetLastQueryProfileFalse()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $this->assertFalse($prof->getLastQueryProfile());
    }

    /**
     * Ensures that getLastQueryProfile() returns the last query profiled, regardless whether it has ended.
     *
     * @return void
     */
    public function testProfilerGetLastQueryProfile()
    {
        $prof = $this->_db->getProfiler()
                ->setEnabled(true);

        $this->_db->query('sql 1');

        $prof->queryStart('sql 2');

        $this->assertType('Zend_Db_Profiler_Query',
            $queryProfile = $prof->getLastQueryProfile());

        $this->assertEquals('sql 2', $queryProfile->getQuery());

        $this->assertFalse($queryProfile->getElapsedSecs());
    }
}
