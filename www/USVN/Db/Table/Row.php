<?php

/**
 * @category   USVN
 * @package    USVN_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class USVN_Db_Table_Row extends Zend_Db_Table_Row {
	/**
     * Getter for camelCaps properties mapped to underscore_word columns.
     *
     * @param string $camel The camelCaps property name; e.g., 'columnName' maps to 'column_name'.
     * @return string The mapped column value.
     */
	public function __get($key)
	{
		$under = $this->_checkKey($key);
		return $this->_data[$under];
	}

	/**
     * Setter for camelCaps properties mapped to underscore_word columns.
     *
     * @param string $camel The camelCaps property name; e.g., 'columnName' maps to 'column_name'.
     * @param mixed $value The value for the property.
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
	public function __set($key, $value)
	{
		$under = $this->_checkKey($key);
		// @todo this should permit a primary key value to be set
		// not all table have an auto-generated primary key
		if (in_array($under, $this->_primary)) {
			require_once 'Zend/Db/Table/Row/Exception.php';
			throw new Zend_Db_Table_Row_Exception("Changing the primary key value(s) is not allowed");
		}

		$this->_data[$under] = $value;
	}

	protected function _checkKey($key)
	{
		if (array_key_exists($key, $this->_data)) {
			return $key;
		}

		$info = $this->_getTable()->info();
		if (array_key_exists($info['fieldPrefix'] . $key, $this->_data)) {
			return $info['fieldPrefix'] . $key;
		}

		throw new Zend_Db_Table_Row_Exception("column '$key' not in row");
	}
}
