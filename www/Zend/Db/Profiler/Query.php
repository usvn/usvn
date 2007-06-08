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
 * @subpackage Profiler
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Query.php 4779 2007-05-11 18:28:57Z darby $
 */


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Profiler
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Profiler_Query
{

    /**
     * SQL query string or user comment, set by $query argument in constructor.
     *
     * @var string
     */
    protected $_query = '';

    /**
     * One of the Zend_Db_Profiler constants for query type, set by $queryType argument in constructor.
     *
     * @var integer
     */
    protected $_queryType = 0;

    /**
     * Unix timestamp with microseconds when instantiated.
     *
     * @var float
     */
    protected $_startedMicrotime = null;

    /**
     * Unix timestamp with microseconds when self::queryEnd() was called.
     *
     * @var integer
     */
    protected $_endedMicrotime = null;

    /**
     * Class constructor.  A query is about to be started, save the query text ($query) and its
     * type (one of the Zend_Db_Profiler::* constants).
     *
     * @param  string  $query
     * @param  integer $queryType
     * @return void
     */
    public function __construct($query, $queryType)
    {
        $this->_query = $query;
        $this->_queryType = $queryType;
        $this->_startedMicrotime = microtime(true);
    }

    /**
     * Ends the query and records the time so that the elapsed time can be determined later.
     *
     * @return void
     */
    public function end()
    {
        $this->_endedMicrotime = microtime(true);
    }

    /**
     * Returns true if and only if the query has ended.
     *
     * @return boolean
     */
    public function hasEnded()
    {
        return $this->_endedMicrotime !== null;
    }

    /**
     * Get the original SQL text of the query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Get the type of this query (one of the Zend_Db_Profiler::* constants)
     *
     * @return integer
     */
    public function getQueryType()
    {
        return $this->_queryType;
    }

    /**
     * Get the elapsed time (in seconds) that the query ran.
     * If the query has not yet ended, false is returned.
     *
     * @return float|false
     */
    public function getElapsedSecs()
    {
        if (null === $this->_endedMicrotime) {
            return false;
        }

        return $this->_endedMicrotime - $this->_startedMicrotime;
    }
}

