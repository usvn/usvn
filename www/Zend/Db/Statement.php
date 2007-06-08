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
 */

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Zend_Db_Statement_Interface
 */
require_once 'Zend/Db/Statement/Interface.php';

/**
 * Abstract class to emulate a PDOStatement for native database adapters.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Statement implements Zend_Db_Statement_Interface
{

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter = null;

    /**
     * The current fetch mode.
     *
     * @var integer
     */
    protected $_fetchMode = Zend_Db::FETCH_ASSOC;

    /**
     * Attributes.
     *
     * @var array
     */
    protected $_attribute = array();

    /**
     * Column result bindings.
     *
     * @var array
     */
    protected $_bindColumn = array();

    /**
     * Query parameter bindings; covers bindParam() and bindValue().
     *
     * @var array
     */
    protected $_bindParam = array();

    /**
     * SQL string split into an array at placeholders.
     *
     * @var array
     */
    protected $_sqlSplit = array();

    /**
     * Parameter placeholders in the SQL string by position in the split array.
     *
     * @var array
     */
    protected $_sqlParam = array();

    /**
     * Constructor for a statement.
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param mixed $sql Either a string or Zend_Db_Select.
     */
    public function __construct($adapter, $sql)
    {
        $this->_adapter = $adapter;
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->__toString();
        }
        $this->_prepSql($sql);
    }

    /**
     * Splits SQL into text and params, sets up $this->_bindParam
     * for replacements.
     *
     * @param string $sql
     * @return void
     *
     * @todo: Parse the string more faithfully so that strings that resemble
     * parameter placeholders but that appear inside string literals or other
     * expressions are not treated as placeholders.
     */
    protected function _prepSql($sql)
    {
        // split into text and params
        $this->_sqlSplit = preg_split('/(\?|\:[a-z_]+)/',
            $sql, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

        // map params
        $this->_sqlParam = array();
        foreach ($this->_sqlSplit as $key => $val) {
            if ($val[0] == ':' || $val[0] == '?') {
                $this->_sqlParam[] = $val;
            }
        }

        // set up for binding
        $this->_bindParam = array();
    }

    /**
     * Joins SQL text and bound params into a string.
     * This emulates SQL parameters by quoting the values directly into the
     * SQL string.
     *
     * @return string
     */
    protected function _joinSql()
    {
        $sql = $this->_sqlSplit;
        foreach ($this->_bindParam as $key => $val) {
            $position = ($key * 2) + 1;
            $sql[$position] = $this->_adapter->quote($val);
        }
        return implode('', $sql);
    }

    /**
     * Bind a column of the statement result set to a PHP variable.
     *
     * @param string $column Name the column in the result set, either by
     *                       position or by name.
     * @param mixed  $param  Reference to the PHP variable containing the value.
     * @param mixed  $type   OPTIONAL
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function bindColumn($column, &$param, $type = null)
    {
        $this->_bindColumn[$column] =& $param;
        return true;
    }

    /**
     * Check sanity of bind parameters.  Throw exceptions if params are
     * not valid.  
     *
     * @param mixed $parameter Name the parameter, either integer or string.
     * @param mixed $variable Reference to PHP variable containing the value.
     * @return integer
     * @throws Zend_Db_Statement_Exception
     */
    protected function _normalizeBindParam($parameter, &$variable, $supportsNumeric = true, $supportsNamed = true)
    {
        $position = null;
        if (is_integer($parameter)) {
            if (!$supportsNumeric) {
                require_once 'Zend/Db/Statement/Exception.php';
                throw new Zend_Db_Statement_Exception("Invalid bind-variable position '$parameter'");
            }
            if ($parameter > 0 && $parameter <= count($this->_sqlParam)) {
                // bind by position, 1-based
                $position = $parameter - 1;
                $this->_bindParam[$position] =& $variable;
            } else {
                require_once 'Zend/Db/Statement/Exception.php';
                throw new Zend_Db_Statement_Exception("Invalid bind-variable position '$parameter'");
            }
        } else if (is_string($parameter))  {
            if (!$supportsNamed) {
                require_once 'Zend/Db/Statement/Exception.php';
                throw new Zend_Db_Statement_Exception("Invalid bind-variable position '$parameter'");
            }
            // bind by name. make sure it has a colon on it.
            if ($parameter[0] != ':') {
                $parameter = ":$parameter";
            }
            // look up its position in the params.
            $position = array_search($parameter, $this->_sqlParam);
            if (is_integer($position)) {
                $this->_bindParam[$position] =& $variable;
            } else {
                require_once 'Zend/Db/Statement/Exception.php';
                throw new Zend_Db_Statement_Exception("Invalid bind-variable position '$parameter'");
            }
        } else {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception('Invalid bind-variable position');
        }

        return $position;
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
     * @throws Zend_Db_Statement_Exception
     */
    public function bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        $this->_normalizeBindParam($parameter, $variable);
        return true;
    }

    /**
     * Binds a value to a parameter.
     *
     * @param mixed $parameter Name the parameter, either integer or string.
     * @param mixed $value     Scalar value to bind to the parameter.
     * @param mixed $type      OPTIONAL Datatype of the parameter.
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function bindValue($parameter, $value, $type = null)
    {
        return $this->bindParam($parameter, $value);
    }

    /**
     * Returns an array containing all of the result set rows.
     *
     * @param int $style OPTIONAL Fetch mode.
     * @param int $col   OPTIONAL Column number, if fetch mode is by column.
     * @return array Collection of rows, each in a format by the fetch mode.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchAll($style = null, $col = null)
    {
        $data = array();
        if ($style === Zend_Db::FETCH_COLUMN && $col === null) {
            $col = 0;
        }
        if ($col === null) {
            while ($row = $this->fetch($style)) {
                $data[] = $row;
            }
        } else {
            while ($val = $this->fetchColumn($col)) {
                $data[] = $val;
            }
        }
        return $data;
    }

    /**
     * Returns a single column from the next row of a result set.
     *
     * @param int $col OPTIONAL Position of the column to fetch.
     * @return string
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchColumn($col = 0)
    {
        $data = array();
        $col = (int) $col;
        $row = $this->fetch(Zend_Db::FETCH_NUM);
        if (is_array($row)) {
            return $row[$col];
        } else {
            return false;
        }
    }

    /**
     * Fetches the next row and returns it as an object.
     *
     * @param string $class  OPTIONAL Name of the class to create.
     * @param array  $config OPTIONAL Constructor arguments for the class.
     * @return mixed One object instance of the specified class.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchObject($class = 'stdClass', array $config = array())
    {
        $obj = new $class($config);
        $row = $this->fetch(Zend_Db::FETCH_ASSOC);
        foreach ($row as $key => $val) {
            $obj->$key = $val;
        }
        return $obj;
    }

    /**
     * Retrieve a statement attribute.
     *
     * @param string $key Attribute name.
     * @return mixed      Attribute value.
     * @throws Zend_Db_Statement_Exception
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->_attribute)) {
            return $this->_attribute[$key];
        }
    }

    /**
     * Set a statement attribute.
     *
     * @param string $key Attribute name.
     * @param mixed  $val Attribute value.
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function setAttribute($key, $val)
    {
        $this->_attribute[$key] = $val;
    }

    /**
     * Set the default fetch mode for this statement.
     *
     * @param int   $mode The fetch mode.
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case Zend_Db::FETCH_NUM:
            case Zend_Db::FETCH_ASSOC:
            case Zend_Db::FETCH_BOTH:
            case Zend_Db::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            default:
                require_once 'Zend/Db/Statement/Exception.php';
                throw new Zend_Db_Statement_Exception('Invalid fetch mode specified');
                break;
        }
    }

}
