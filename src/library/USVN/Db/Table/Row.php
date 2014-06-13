<?php
/**
 * A row into table
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info/
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage Row
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id $
 */
class USVN_Db_Table_Row extends Zend_Db_Table_Row_Abstract {
    /**
     * Transform a column name from the user-specified form
     * to the physical form used in the database.
     * You can override this method in a custom Row class
     * to implement column name mappings, for example inflection.
     *
     * @param string $columnName Column name given.
     * @return string The column name after transformation applied (none by default).
     * @throws Zend_Db_Table_Row_Exception if the $columnName is not a string.
     */
    protected function _transformColumn($columnName)
	{
		if (array_key_exists($columnName, $this->_data)) {
			return $columnName;
		}
		$info = $this->_getTable()->info();
		if (array_key_exists($info['fieldPrefix'] . $columnName, $this->_data)) {
			return $info['fieldPrefix'] . $columnName;
		}
		USVNLogObject('Row Data', $this->_data);
		USVNLogObject('Field Prefix', $info['fieldPrefix']);
		throw new Zend_Db_Table_Row_Exception("column '$columnName' not in row");
	}
}
