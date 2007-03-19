<?php

/**
 * Class for SQL table interface.
 *
 * @category   USVN
 * @package	USVN_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license	http://framework.zend.com/license/new-bsd	 New BSD License
 */
abstract class USVN_Db_Table extends Zend_Db_Table {
	/**
	 * The table prefix.
	 *
	 * Must be the same for all tables and is configurable via .ini (database.prefixe)
	 *
	 * @var string
	 * @access static
	 */
	static public $prefix = "";

	/**
	 * The field's prefix for this table.
	 *
	 * All fields must have the same prefixe.
	 * Configurable via your class declaration.
	 *
	 * @var string
	 * @see Users
	 */
	protected $_fieldPrefix = "";

	/**
	 * Name of the Row object to instantiate when needed.
	 *
	 * @var string
	 */
	protected $_tableRowName = "USVN_Db_Table_Row";

	/**
	 * Name of the Rowset object to instantiate when needed.
	 *
	 * @var string
	 */
	protected $_tableRowsetName = "USVN_Db_Table_Rowset";

	/**
	 * The primary key column (underscore format).
	 *
	 * Without your prefixe...
	 *
	 * @var string
	 * @see Users
	 */
	protected $_primary = 'id';



	/**
	 * Populate static properties for this table module.
	 *
	 * @return void
	 * @throws Zend_Db_Table_Exception
	 */
	protected function _setup($config = array())
	{
		// get the table name
		if (isset($config['name'])) {
			$this->_name = $config['name'];
		}
		if (! $this->_name) {
			$this->_name = self::$_inflector->underscore(get_class($this));
		}

		$this->_name = self::$prefix . $this->_name;
		$this->_primary = $this->_fieldPrefix . $this->_primary;

		parent::_setup($config);
	}

	/**
	 * Returns table information.
	 *
	 * @return array
	 */
	public function info()
	{
		$info = parent::info();
		$info = array_merge($info, array("tableRowName" => $this->_tableRowName));
		$info = array_merge($info, array("tableRowsetName" => $this->_tableRowsetName));
		$info = array_merge($info, array("fieldPrefix" => $this->_fieldPrefix));
		return $info;
	}



	// -----------------------------------------------------------------
	//
	// Retrieval
	//
	// -----------------------------------------------------------------

	/**
	 * Fetches rows by primary key.
	 *
	 * @param scalar|array $val The value of the primary key.
	 * @return array Row(s) which matched the primary key value.
	 */
	public function find($val)
	{
		$val = (array) $val;
		$key = $this->_primary;
		if (count($val) > 1) {
			$where = array(
			"$key IN(?)" => $val,
			);
			$order = "$key ASC";
			return $this->fetchAll($where, $order);
		} else {
			$where = array(
			"$key = ?" => (isset($val[0]) ? $val[0] : ''),
			);
			return $this->fetchRow($where);
		}
	}

	/**
	 * Fetches all rows.
	 *
	 * Honors the Zend_Db_Adapter_Abstract fetch mode.
	 *
	 * @param string|array $where  OPTIONAL An SQL WHERE clause.
	 * @param string|array $order  OPTIONAL An SQL ORDER clause.
	 * @param int		  $count  OPTIONAL An SQL LIMIT count.
	 * @param int		  $offset OPTIONAL An SQL LIMIT offset.
	 * @return Zend_Db_Table_Rowset The row results per the Zend_Db_Adapter_Abstract fetch mode.
	 */
	public function fetchAll($where = null, $order = null, $count = null, $offset = null)
	{
		return new $this->_tableRowsetName(array(
		'db'	=> $this->_db,
		'table' => $this,
		'data'  => $this->_fetch('All', $where, $order, $count, $offset),
		));
	}

	/**
	 * Fetches one row.
	 *
	 * Honors the Zend_Db_Adapter_Abstract fetch mode.
	 *
	 * @param string|array $where OPTIONAL An SQL WHERE clause.
	 * @param string|array $order OPTIONAL An SQL ORDER clause.
	 * @return Zend_Db_Table_Row The row results per the Zend_Db_Adapter_Abstract fetch mode.
	 */
	public function fetchRow($where = null, $order = null)
	{
		return new $this->_tableRowName(array(
		'db'	=> $this->_db,
		'table' => $this,
		'data'  => $this->_fetch('Row', $where, $order, 1),
		));
	}

	/**
	 * Fetches a new blank row (not from the database).
	 *
	 * @return Zend_Db_Table_Row
	 */
	public function fetchNew()
	{
		$keys = array_keys($this->_cols);
		$vals = array_fill(0, count($keys), null);
		return new $this->_tableRowName(array(
		'db'	=> $this->_db,
		'table' => $this,
		'data'  => array_combine($keys, $vals)
		));
	}

}