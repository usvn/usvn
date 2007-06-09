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
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Mysqli.php 5065 2007-05-31 05:04:21Z bkarwin $
 */


/**
 * @see Zend_Db_Statement
 */
require_once 'Zend/Db/Statement.php';


/**
 * Extends for Mysqli
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Statement_Mysqli extends Zend_Db_Statement
{

    /**
     * The mysqli_stmt object.
     *
     * @var mysqli_stmt
     */
    protected $_stmt;

    /**
     * Column names.
     *
     * @var array
     */
    protected $_keys;

    /**
     * Fetched result values.
     *
     * @var array
     */
    protected $_values;

    /**
     * @param  string $sql
     * @return void
     * @throws Zend_Db_Statement_Mysqli_Exception
     */
    public function _prepSql($sql)
    {
        parent::_prepSql($sql);

        $mysqli = $this->_adapter->getConnection();

        $this->_stmt = $mysqli->prepare($sql);

        if ($this->_stmt === false || $mysqli->errno) {
            /**
             * @see Zend_Db_Statement_Mysqli_Exception
             */
            require_once 'Zend/Db/Statement/Mysqli/Exception.php';
            throw new Zend_Db_Statement_Mysqli_Exception("Mysqli prepare error: " . $mysqli->error);
        }
    }

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return bool
     */
    public function closeCursor()
    {
        if (!$this->_stmt) {
            return false;
        }
        $this->_stmt->close();
        return true;
    }

    /**
     * Returns the number of columns in the result set.
     * Returns null if the statement has no result set metadata.
     *
     * @return int The number of columns.
     */
    public function columnCount()
    {
        if (isset($this->_meta) && $this->_meta) {
            return $this->_meta->field_count;
        }
        return 0;
    }

    /**
     * Retrieves the error code, if any, associated with the last operation on
     * the statement handle.
     *
     * @return string error code.
     */
    public function errorCode()
    {
        if (!$this->_stmt) {
            return null;
        }
        return substr($this->_stmt->sqlstate, 0, 5);
    }

    /**
     * Retrieves an array of error information, if any, associated with the
     * last operation on the statement handle.
     *
     * @return array
     */
    public function errorInfo()
    {
        if (!$this->_stmt) {
            return null;
        }
        return array(
            substr($this->_stmt->sqlstate, 0, 5),
            $this->_stmt->errno,
            $this->_stmt->error,
        );
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL Values to bind to parameter placeholders.
     * @return bool
     * @throws Zend_Db_Statement_Mysqli_Exception
     */
    public function execute(array $params = null)
    {
        if (!$this->_stmt) {
            return false;
        }
        // retain metadata
        $this->_meta = $this->_stmt->result_metadata();
        if ($this->_stmt->errno) {
            /**
             * @see Zend_Db_Statement_Mysqli_Exception
             */
            require_once 'Zend/Db/Statement/Mysqli/Exception.php';
            throw new Zend_Db_Statement_Mysqli_Exception("Mysqli statement metadata error: " . $this->_stmt->error);
        }

        // statements that have no result set do not return metadata
        if ($this->_meta !== false) {

            // get the column names that will result
            $this->_keys = array();
            foreach ($this->_meta->fetch_fields() as $col) {
                $this->_keys[] = $this->_adapter->foldCase($col->name);
            }

            // set up a binding space for result variables
            $this->_values = array_fill(0, count($this->_keys), null);

            // set up references to the result binding space.
            // just passing $this->_values in the call_user_func_array()
            // below won't work, you need references.
            $refs = array();
            foreach ($this->_values as $i => &$f) {
                $refs[$i] = &$f;
            }

            // bind to the result variables
            call_user_func_array(
                array($this->_stmt, 'bind_result'),
                $this->_values
            );
        }

        // if no params were given as an argument to execute(),
        // then default to the _bindParam array
        if (!$params) {
            $params = $this->_bindParam;
        }
        // send $params as input parameters to the statement
        if ($params) {
            array_unshift($params, str_repeat('s', count($params)));
            call_user_func_array(
                array($this->_stmt, 'bind_param'),
                $params
            );
        }

        // execute the statement
        $retval = $this->_stmt->execute();
        if ($retval === false) {
            /**
             * @see Zend_Db_Statement_Mysqli_Exception
             */
            require_once 'Zend/Db/Statement/Mysqli/Exception.php';
            throw new Zend_Db_Statement_Mysqli_Exception("Mysqli statement execute error : " . $this->_stmt->error);
        }
        return $retval;
    }

    /**
     * Fetches a row from the result set.
     *
     * @param int $style  OPTIONAL Fetch mode for this fetch operation.
     * @param int $cursor OPTIONAL Absolute, relative, or other.
     * @param int $offset OPTIONAL Number for absolute or relative cursors.
     * @return mixed Array, object, or scalar depending on fetch mode.
     * @throws Zend_Db_Statement_Mysqli_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        if (!$this->_stmt) {
            return null;
        }
        // fetch the next result
        $retval = $this->_stmt->fetch();
        switch ($retval) {
        case null: // end of data
        case false: // error occurred
            $this->closeCursor();
            return $retval;
        default:
            // fallthrough
        }

        // make sure we have a fetch mode
        if ($style === null) {
            $style = $this->_fetchMode;
        }

        // dereference the result values, otherwise things like fetchAll()
        // return the same values for every entry (because of the reference).
        $values = array();
        foreach ($this->_values as $key => $val) {
            $values[] = $val;
        }

        // bind back to external references
        foreach ($this->_bindColumn as $key => &$val) {
            if (is_integer($key)) {
                // bind by column position
                // note that vals are 0-based, but cols are 1-based
                $val = $values[$key-1];
            } else {
                // bind by column name
                $i = array_search($key, $this->_keys);
                $val = $values[$i];
            }
        }

        $data = false;
        switch ($style) {
            case Zend_Db::FETCH_NUM:
                $data = $values;
                break;
            case Zend_Db::FETCH_ASSOC:
                $data = array_combine($this->_keys, $values);
                break;
            case Zend_Db::FETCH_BOTH:
                $assoc = array_combine($this->_keys, $values);
                $data = array_merge($values, $assoc);
                break;
            case Zend_Db::FETCH_OBJ:
                $data = (object) array_combine($this->_keys, $values);
                break;
            case Zend_Db::FETCH_BOUND:
                /**
                 * @see Zend_Db_Statement_Mysqli_Exception
                 */
                require_once 'Zend/Db/Statement/Mysqli/Exception.php';
                throw new Zend_Db_Statement_Mysqli_Exception("FETCH_BOUND is not supported yet");
                break;
            default:
                /**
                 * @see Zend_Db_Statement_Mysqli_Exception
                 */
                require_once 'Zend/Db/Statement/Mysqli/Exception.php';
                throw new Zend_Db_Statement_Mysqli_Exception("Invalid fetch mode specified");
                break;
        }

        return $data;
    }

    /**
     * Retrieves the next rowset (result set) for a SQL statement that has
     * multiple result sets.  An example is a stored procedure that returns
     * the results of multiple queries.
     *
     * @return bool
     * @throws Zend_Db_Statement_Mysqli_Exception
     */
    public function nextRowset()
    {
        /**
         * @see Zend_Db_Statement_Mysqli_Exception
         */
        require_once 'Zend/Db/Statement/Mysqli/Exception.php';
        throw new Zend_Db_Statement_Mysqli_Exception(__FUNCTION__.'() is not implemented');
    }

    /**
     * Returns the number of rows affected by the execution of the
     * last INSERT, DELETE, or UPDATE statement executed by this
     * statement object.
     *
     * @return int     The number of rows affected.
     */
    public function rowCount()
    {
        if (!$this->_adapter) {
            return null;
        }
        $mysqli = $this->_adapter->getConnection();
        return $mysqli->affected_rows;
    }

}
