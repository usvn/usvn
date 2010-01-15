<?php
/**
 * Model for the `Hooks` table
 *
 * Extends USVN_Db_Table for magic configuration and methods
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info/
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage Table
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id $
 */
class USVN_Db_Table_Hooks extends USVN_Db_Table {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without prefix
	 *
	 * @var string
	 */
	protected $_primary = "hooks_id";

	/**
	 * The field's prefix for this table.
	 *
	 * @var string
	 */
	protected $_fieldPrefix = "hooks_";

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "hooks";

	/**
	 * Name of the Row object to instantiate when needed.
	 *
	 * @var string
	 */
	//protected $_rowClass = "USVN_Db_Table_Row_Hooks";

	/**
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_ProjectsToHooks");

	/**
	 * Overload insert method to check some data before insert
	 *
	 * @param array $data
	 * @return integer the last insert ID.
	 */
	public function insert(array $data)
	{
		//$this->checkFileExists($data['path']);
		return parent::insert($data);
	}

	/**
	 * Return the hooks by hooks_id
	 *
	 * @param string $id
	 * @return USVN_Db_Table_Row
	 */
	public function findByHooksId($id)
	{
		$db = $this->getAdapter();
		$where = $db->quoteInto("hooks_id = ?", $id);
		return $this->fetchRow($where, "hooks_id");
	}
}
