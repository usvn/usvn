<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/**
 * @see Zend_Db_Statement
 */
require_once 'Zend/Db/Statement.php';

/**
 * Extends for DB2 native adapter.
 *
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @author     Joscha Feth <jffeth@de.ibm.com>
 * @author     Salvador Ledezma <ledezma@us.ibm.com>
 */
class Zend_Db_Statement_Db2 extends Zend_Db_Statement
{
    /**
     * Statement resource handle.
     */
    protected $_stmt = null;

    /**
     * Column names.
     */
    protected $_keys;

    /**
     * Fetched result values.
     */
    protected $_values;

    /**
     * Prepare a statement handle.
     *
     * @param string $sql
     * @return void
     * @throws Zend_Db_Statement_Db2_Exception
     */
    public function _prepSql($sql)
    {
        parent::_prepSql($sql);
        $connection = $this->_adapter->getConnection();

        $this->_stmt = db2_prepare($connection, $sql);

        if (!$this->_stmt) {
            require_once 'Zend/Db/Statement/Db2/Exception.php';
            throw new Zend_Db_Statement_Db2_Exception(
                db2_stmt_errormsg(),
                db2_stmt_error()
            );
        }
    }

    /**
     * Binds a parameter to the specified variable name.
     *
     * @param mixed $parameter Name the parameter, either integer or string.
     * @param mixed $variable  Reference to PHP variable containing the value.
     * @param mixed $type      OPTIONAL Datatype of SQL parameter.
     * @param mixed $length    OPTIONAL Length of SQL parameter.
     * @param mixed $options   OPTIONAL Other options.
     * @return bool
     * @throws Zend_Db_Statement_Db2_Exception
     */
    public function bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        $position = $this->_normalizeBindParam($parameter, $variable, true, true);

        if ($type === null) {
            $type = DB2_PARAM_IN;
        }

        if (isset($options['data-type'])) {
            $datatype = $options['data-type'];
        } else {
            $datatype = DB2_CHAR;
        }

        if (!db2_bind_param($this->_stmt, $position, "variable", $type, $datatype)) {
            require_once 'Zend/Db/Statement/Db2/Exception.php';
            throw new Zend_Db_Statement_Db2_Exception(
                db2_stmt_errormsg($this->_stmt),
                db2_stmt_error($this->_stmt)
            );
        }

        return true;
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
        db2_free_stmt($this->_stmt);
        $this->_stmt = false;
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
        if (!$this->_stmt) {
            return false;
        }
        return db2_num_fields($this->_stmt);
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
            return '0000';
        }

        return db2_stmt_error($this->_stmt);
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
            return array(false, 0, '');
        }

        /*
         * Return three-valued array like PDO.  But DB2 does not distinguish
         * between SQLCODE and native RDBMS error code, so repeat the SQLCODE.
         */
        return array(
            db2_stmt_error($this->_stmt),
            db2_stmt_error($this->_stmt),
            db2_stmt_errormsg($this->_stmt)
        );
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL Values to bind to parameter placeholders.
     * @return bool
     * @throws Zend_Db_Statement_Db2_Exception
     */
    public function execute(array $params = array())
    {
        if (!$this->_stmt) {
            $connection = $this->_adapter->getConnection();
            $sql = $this->_joinSql();
            $this->_stmt = db2_prepare($connection, $sql);
        }

        if (!$this->_stmt) {
            require_once 'Zend/Db/Statement/Db2/Exception.php';
            throw new Zend_Db_Statement_Db2_Exception(
                db2_conn_errormsg($connection),
                db2_conn_error($connection));
        }

        $retval = @db2_execute($this->_stmt, $params);

        if ($retval === false) {
            require_once 'Zend/Db/Statement/Db2/Exception.php';
            throw new Zend_Db_Statement_Db2_Exception(
                db2_stmt_errormsg($this->_stmt),
                db2_stmt_error($this->_stmt));
        }

        $this->_keys = array();
        if ($field_num = $this->columnCount()) {
            for ($i = 0; $i < $field_num; $i++) {
                $name = db2_field_name($this->_stmt, $i);
                $this->_keys[] = $name;
            }
        }

        $this->_values = array();
        if ($this->_keys) {
            $this->_values = array_fill(0, count($this->_keys), null);
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
     * @throws Zend_Db_Statement_Db2_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        if (!$this->_stmt) {
            return false;
        }

        if ($style === null) {
            $style = $this->_fetchMode;
        }

        switch ($style) {
            case Zend_Db::FETCH_NUM :
                $fetch_function = "db2_fetch_array";
                break;
            case Zend_Db::FETCH_ASSOC :
                $fetch_function = "db2_fetch_assoc";
                break;
            case Zend_Db::FETCH_BOTH :
                $fetch_function = "db2_fetch_both";
                break;
            case Zend_Db::FETCH_OBJ :
                $fetch_function = "db2_fetch_object";
                break;
            default:
                require_once 'Zend/Db/Statement/Db2/Exception.php';
                throw new Zend_Db_Statement_Db2_Exception('invalid fetch mode specified');
                break;
        }

        $row = $fetch_function($this->_stmt);
        return $row;
    }

    /**
     * Fetches the next row and returns it as an object.
     *
     * @param string $class  OPTIONAL Name of the class to create.
     * @param array  $config OPTIONAL Constructor arguments for the class.
     * @return mixed One object instance of the specified class.
     */
    public function fetchObject($class = 'stdClass', array $config = array())
    {
        $obj = $this->fetch(Zend_Db::FETCH_OBJ);
        return $obj;
    }

    /**
     * Retrieves the next rowset (result set) for a SQL statement that has
     * multiple result sets.  An example is a stored procedure that returns
     * the results of multiple queries.
     *
     * @return bool
     * @throws Zend_Db_Statement_Db2_Exception
     */
    public function nextRowset()
    {
        /**
         * @see Zend_Db_Statement_Db2_Exception
         */
        require_once 'Zend/Db/Statement/Db2/Exception.php';
        throw new Zend_Db_Statement_Db2_Exception(__FUNCTION__ . '() is not implemented');
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
        if (!$this->_stmt) {
            return false;
        }

        $num = @db2_num_rows($this->_stmt);

        if ($num === false) {
            return 0;
        }

        return $num;
    }

     /**
     * Returns an array containing all of the result set rows.
     *
     * @param int $style OPTIONAL Fetch mode.
     * @param int $col   OPTIONAL Column number, if fetch mode is by column.
     * @return array Collection of rows, each in a format by the fetch mode.
     * @throws Zend_Db_Statement_Exception
     *
     * Behaves like parent, but if limit()
     * is used, the final result removes the extra column
     * 'zend_db_rownum'
     */
    public function fetchAll($style = null, $col = null)
    {
        $data = parent::fetchAll($style, $col);
        $results = array();
	$remove = $this->_adapter->foldCase('ZEND_DB_ROWNUM');

        foreach ($data as $row) {
            if (is_array($row) && array_key_exists($remove, $row)) {
                unset($row[$remove]);
            }
            $results[] = $row;
        }
        return $results;
    }
}
