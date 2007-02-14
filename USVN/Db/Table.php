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
	static public $_prefix = "";
	protected $_tableRowName = "Zend_Db_Table_Row";
	protected $_tableRowsetName = "Zend_Db_Table_Rowset";

	/**
	 * Populate static properties for this table module.
	 *
	 * @return void
	 * @throws Zend_Db_Table_Exception
	 */
	protected function _setup()
	{
		parent::_setup();
		
		if (strcmp(substr($this->_name, 0, strlen(self::$_prefix)), self::$_prefix)) {
			$this->_name = self::$_prefix . $this->_name;
		}
	}

	/**
	 * Fetches all rows.
	 *
	 * Honors the Zend_Db_Adapter fetch mode.
	 *
	 * @param string|array $where An SQL WHERE clause.
	 * @param string|array $order An SQL ORDER clause.
	 * @param int $count An SQL LIMIT count.
	 * @param int $offset An SQL LIMIT offset.
	 * @return mixed The row results per the Zend_Db_Adapter fetch mode.
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
	 * Honors the Zend_Db_Adapter fetch mode.
	 *
	 * @param string|array $where An SQL WHERE clause.
	 * @param string|array $order An SQL ORDER clause.
	 * @return mixed The row results per the Zend_Db_Adapter fetch mode.
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
		'data'  => array_combine($keys, $vals),

		));
	}
}