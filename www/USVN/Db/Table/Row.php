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
	public function __get($camel)
	{
		$under = $this->_uncamelize($camel);
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
	public function __set($camel, $value)
	{
		$under = $this->_uncamelize($camel);
		if ($under == $this->_info['primary']) {
			throw new Zend_Db_Table_Row_Exception("not allowed to change primary key value");
		} else {
			$this->_data[$under] = $value;
		}
	}

	protected function _uncamelize($camel)
	{
		$cols = $this->_info['cols'];
		$under = array_search($camel, $cols);
		if ($under === false) {
			$under = isset($cols[$camel]) ? $camel : false;
		}
		if ($under === false) {
			$tmp = $this->_info['fieldPrefix'] . $camel;
			$under = isset($cols[$tmp]) ? $tmp : false;
		}
		if ($under === false) {
			throw new Zend_Db_Table_Row_Exception("column '$camel' not in row");
		}
		return $under;
	}
}
